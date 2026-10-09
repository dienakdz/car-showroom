STOP_WORDS = {
    "ban",
    "chiec",
    "cho",
    "co",
    "con",
    "dep",
    "di",
    "giup",
    "la",
    "minh",
    "muon",
    "nao",
    "rat",
    "tim",
    "toi",
    "va",
    "xe",
    "xem",
}

ALIASES = {
    "mec": "mercedes-benz",
    "mẹc": "mercedes-benz",
    "mer": "mercedes-benz",
    "mac da": "mazda",
    "bim": "bmw",
    "lech xu": "lexus",
    "huyndai": "hyundai",
    "huyen dai": "hyundai",
    "pot che": "porsche",
    "cx nam": "cx-5",
}

CONDITION_TERMS = {
    "new": ("xe moi", "moi 100", "new", "moi"),
    "used": ("xe cu", "da qua su dung", "sieu luot", "used", "cu"),
    "cpo": ("cpo", "certified pre-owned", "xe chung nhan"),
}

BODY_TYPE_TERMS = {
    "suv": ("suv", "gam cao"),
    "sedan": ("sedan",),
    "hatchback": ("hatchback",),
    "pickup": ("pickup", "ban tai"),
}

FUEL_TYPE_TERMS = {
    "gasoline": ("gasoline", "may xang", "xe xang"),
    "diesel": ("diesel", "may dau", "xe dau"),
    "hybrid": ("hybrid", "xang lai dien"),
    "electric": ("electric", "xe dien", "thuan dien", "ev"),
}

TRANSMISSION_TERMS = {
    "automatic": ("automatic", "so tu dong"),
    "manual": ("manual", "so san"),
    "cvt": ("cvt", "vo cap"),
}

DRIVETRAIN_TERMS = {
    "fwd": ("fwd", "dan dong cau truoc", "cau truoc"),
    "rwd": ("rwd", "dan dong cau sau", "cau sau"),
    "awd": ("awd", "dan dong 4 banh toan thoi gian"),
    "4wd": ("4wd", "4x4", "hai cau", "2 cau"),
}

COLOR_TERMS = {
    "obsidian-black": ("mau den", "xe den", "black"),
    "pearl-white": ("mau trang", "xe trang", "white"),
    "candy-red": ("mau do", "xe do", "red"),
    "ocean-blue": ("mau xanh", "xe xanh", "xanh duong", "blue"),
}
