<div class="d-flex justify-content-center">
    <a href="{{ route('investments.edit', $row->id) }}" class="btn btn-primary btn-sm mr-2">
        <i class="fa fa-edit"></i>
    </a>
    <a href="{{ route('investments.show', $row->id) }}" class="btn btn-info btn-sm mr-2">
        <i class="fa fa-eye"></i>
    </a>
    <form action="{{ route('investments.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>
