@extends('admin.layouts.app')

@section('title', 'Lead CRM')

@section('admin-content')
    <div class="my-listing-table wrap-listing admin-listing-shell">
        <div class="cart-table">
            <div class="title-listing">
                <div>
                    <h5>Lead pipeline</h5>
                    <div class="text">CRM queue cho lead tu unit detail, trim, finance va contact form.</div>
                </div>
                <form method="GET" class="admin-template-toolbar admin-template-toolbar-wide">
                    <div class="box-ip-search">
                        <span class="icon"><i class="fa fa-search"></i></span>
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Tim theo ten, phone, email">
                    </div>
                    <div class="admin-filter-box">
                        <select name="status">
                            <option value="">Tat ca status</option>
                            @foreach (['new', 'contacted', 'qualified', 'booked', 'closed', 'lost'] as $status)
                                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ strtoupper($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-filter-box">
                        <select name="source">
                            <option value="">Tat ca source</option>
                            @foreach (['unit_detail', 'trim_page', 'finance', 'trade_in', 'contact'] as $source)
                                <option value="{{ $source }}" @selected($filters['source'] === $source)>{{ $source }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-filter-box">
                        <select name="assigned_to">
                            <option value="">Tat ca staff</option>
                            @foreach ($staffUsers as $staff)
                                <option value="{{ $staff->id }}" @selected($filters['assigned_to'] === $staff->id)>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-table-btn">Loc</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Source</th>
                        <th>Context</th>
                        <th>Status</th>
                        <th>Assignment</th>
                        <th>Notes / Appointments</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        @php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)
                        <tr>
                            <td>
                                <div class="shop-cart-product admin-plain-product">
                                    <div class="shop-product-cart-img admin-symbol-badge">
                                        {{ strtoupper(substr($lead->name, 0, 1)) }}
                                    </div>
                                    <div class="shop-product-cart-info">
                                        <h3>{{ $lead->name }}</h3>
                                        <p>{{ $lead->phone }}{{ $lead->email ? ' / ' . $lead->email : '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span>{{ $lead->source }}</span></td>
                            <td>
                                <span>
                                    {{ trim(collect([
                                        $contextTrim?->model?->make?->name,
                                        $contextTrim?->model?->name,
                                        $contextTrim?->name,
                                    ])->filter()->implode(' ')) ?: 'Lien he chung' }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $lead->status }}">{{ strtoupper($lead->status) }}</span>
                            </td>
                            <td><span>{{ $lead->assignedTo?->name ?? 'Chua phan cong' }}</span></td>
                            <td><span>{{ $lead->notes_count }} note / {{ $lead->appointments_count }} lich</span></td>
                            <td>
                                <a href="{{ route('admin.leads.show', $lead) }}" class="admin-table-btn">Mo lead</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="admin-empty-state">Chua co lead nao.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $leads->links('admin.partials.pagination') }}
        </div>
    </div>
@endsection
