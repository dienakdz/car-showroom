import json
from typing import Any

import pymysql
from pymysql.cursors import DictCursor

from app.config import Settings
from app.models import CarDocument


class CarRepository:
    def __init__(self, settings: Settings) -> None:
        self.settings = settings

    def _connect(self) -> pymysql.Connection:
        return pymysql.connect(
            host=self.settings.db_host,
            port=self.settings.db_port,
            user=self.settings.db_username,
            password=self.settings.db_password,
            database=self.settings.db_database,
            charset="utf8mb4",
            cursorclass=DictCursor,
            autocommit=True,
            connect_timeout=3,
            read_timeout=8,
            write_timeout=3,
        )

    def ping(self) -> None:
        connection = self._connect()
        try:
            with connection.cursor() as cursor:
                cursor.execute("SELECT 1")
                cursor.fetchone()
        finally:
            connection.close()

    def load_public_cars(self) -> list[CarDocument]:
        connection = self._connect()
        try:
            statuses = self._public_statuses(connection)
            cars = self._load_base_cars(connection, statuses)
            if not cars:
                return []

            trim_ids = sorted({int(car["trim_id"]) for car in cars})
            features = self._load_features(connection, trim_ids)
            attributes = self._load_attributes(connection, trim_ids)

            documents: list[CarDocument] = []
            for car in cars:
                trim_id = int(car["trim_id"])
                documents.append(
                    CarDocument(
                        **car,
                        features=features.get(trim_id, []),
                        attributes=attributes.get(trim_id, {}),
                    )
                )

            return documents
        finally:
            connection.close()

    def _public_statuses(self, connection: pymysql.Connection) -> list[str]:
        with connection.cursor() as cursor:
            cursor.execute(
                "SELECT value_json FROM settings WHERE `key` = %s LIMIT 1",
                ("inventory.show_on_hold_public",),
            )
            row = cursor.fetchone()

        if row is None:
            return ["available"]

        value = row.get("value_json")
        if isinstance(value, str):
            try:
                value = json.loads(value)
            except json.JSONDecodeError:
                value = {}

        show_on_hold = isinstance(value, dict) and bool(value.get("enabled", False))
        return ["available", "on_hold"] if show_on_hold else ["available"]

    def _load_base_cars(
        self,
        connection: pymysql.Connection,
        statuses: list[str],
    ) -> list[dict[str, Any]]:
        placeholders = ", ".join(["%s"] * len(statuses))
        query = f"""
            SELECT
                cu.id AS car_unit_id,
                cu.trim_id,
                cu.stock_code,
                cu.condition,
                cu.year,
                cu.mileage,
                cu.price,
                cu.published_at,
                mk.name AS make,
                mk.slug AS make_slug,
                md.name AS model,
                md.slug AS model_slug,
                tr.name AS trim,
                tr.slug AS trim_slug,
                COALESCE(tr.description, '') AS description,
                bt.name AS body_type,
                bt.slug AS body_type_slug,
                ft.name AS fuel_type,
                ft.slug AS fuel_type_slug,
                ts.name AS transmission,
                ts.slug AS transmission_slug,
                dt.name AS drivetrain,
                dt.slug AS drivetrain_slug,
                ec.name AS exterior_color,
                ec.slug AS exterior_color_slug,
                ic.name AS interior_color,
                ic.slug AS interior_color_slug
            FROM car_units AS cu
            INNER JOIN trims AS tr ON tr.id = cu.trim_id
            INNER JOIN models AS md ON md.id = tr.model_id
            INNER JOIN makes AS mk ON mk.id = md.make_id
            LEFT JOIN body_types AS bt ON bt.id = cu.body_type_id
            LEFT JOIN fuel_types AS ft ON ft.id = cu.fuel_type_id
            LEFT JOIN transmissions AS ts ON ts.id = cu.transmission_id
            LEFT JOIN drivetrains AS dt ON dt.id = cu.drivetrain_id
            LEFT JOIN colors AS ec ON ec.id = cu.exterior_color_id
            LEFT JOIN colors AS ic ON ic.id = cu.interior_color_id
            WHERE cu.deleted_at IS NULL
              AND cu.published_at IS NOT NULL
              AND cu.status IN ({placeholders})
            ORDER BY cu.published_at DESC, cu.id DESC
        """

        with connection.cursor() as cursor:
            cursor.execute(query, statuses)
            return list(cursor.fetchall())

    def _load_features(
        self,
        connection: pymysql.Connection,
        trim_ids: list[int],
    ) -> dict[int, list[str]]:
        placeholders = ", ".join(["%s"] * len(trim_ids))
        query = f"""
            SELECT tf.trim_id, f.name
            FROM trim_feature AS tf
            INNER JOIN features AS f ON f.id = tf.feature_id
            WHERE tf.trim_id IN ({placeholders})
            ORDER BY tf.trim_id, f.name
        """
        result: dict[int, list[str]] = {}

        with connection.cursor() as cursor:
            cursor.execute(query, trim_ids)
            for row in cursor.fetchall():
                result.setdefault(int(row["trim_id"]), []).append(str(row["name"]))

        return result

    def _load_attributes(
        self,
        connection: pymysql.Connection,
        trim_ids: list[int],
    ) -> dict[int, dict[str, str | float | bool]]:
        placeholders = ", ".join(["%s"] * len(trim_ids))
        query = f"""
            SELECT
                tav.trim_id,
                a.code,
                a.type,
                tav.value_string,
                tav.value_number,
                tav.value_boolean
            FROM trim_attribute_values AS tav
            INNER JOIN attributes AS a ON a.id = tav.attribute_id
            WHERE tav.trim_id IN ({placeholders})
            ORDER BY tav.trim_id, a.sort_order
        """
        result: dict[int, dict[str, str | float | bool]] = {}

        with connection.cursor() as cursor:
            cursor.execute(query, trim_ids)
            for row in cursor.fetchall():
                value = self._attribute_value(row)
                if value is None:
                    continue
                result.setdefault(int(row["trim_id"]), {})[str(row["code"])] = value

        return result

    @staticmethod
    def _attribute_value(row: dict[str, Any]) -> str | float | bool | None:
        attribute_type = row["type"]
        if attribute_type == "string":
            return row["value_string"]
        if attribute_type == "number" and row["value_number"] is not None:
            return float(row["value_number"])
        if attribute_type == "boolean" and row["value_boolean"] is not None:
            return bool(row["value_boolean"])
        return None
