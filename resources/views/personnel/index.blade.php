@extends('layouts.app')

@section('title', 'Personnel Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Personnel Management</h1>
        <div>
            <a href="{{ route('personnel.export') }}" class="btn btn-info btn-sm">
                <i class="fas fa-download mr-1"></i> Export
            </a>
            <a href="{{ route('personnel.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus mr-1"></i> Add Personnel
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('personnel.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search by name or mRID..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="start_date" 
                           value="{{ request('start_date') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="end_date" 
                           value="{{ request('end_date') }}" placeholder="To Date">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Personnel Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Personnel List</h6>
            <span class="badge badge-primary">Total: {{ $personnel->total() }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>mRID</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>Finish Date</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personnel as $person)
                        <tr>
                            <td>{{ $person->id }}</td>
                            <td>
                                <code>{{ $person->erpPersonnel->erpPerson->mrid ?? 'N/A' }}</code>
                            </td>
                            <td>
                                {{ $person->erpPersonnel->erpPerson->first_name ?? '' }} 
                                {{ $person->erpPersonnel->erpPerson->last_name ?? '' }}
                            </td>
                            <td>{{ $person->erpPersonnel->start_date ?? 'N/A' }}</td>
                            <td>{{ $person->erpPersonnel->finish_date ?? 'Active' }}</td>
                            <td>
                                @if($person->erpPersonnel->finish_date && $person->erpPersonnel->finish_date < now())
                                    <span class="badge badge-danger">Inactive</span>
                                @else
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                            <td>{{ $person->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('personnel.show', $person->id) }}" 
                                       class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('personnel.edit', $person->id) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('personnel.destroy', $person->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this personnel?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i><br>
                                    No personnel records found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $personnel->links() }}
            </div>
        </div>
    </div>
</div>
@endsection