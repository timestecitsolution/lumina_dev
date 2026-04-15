@csrf

<div class="mb-3">
    <label class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" value="{{ old('name', $investor->name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" value="{{ old('email', $investor->email ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', $investor->phone ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Company</label>
    <input type="text" name="company" value="{{ old('company', $investor->company ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="3">{{ old('address', $investor->address ?? '') }}</textarea>
</div>

<div class="mb-3 form-check">
    <input type="hidden" name="assigned_employee_from_investor" value="0">
    <input type="checkbox" class="form-check-input" id="assigned_employee_from_investor" name="assigned_employee_from_investor" value="1"
        {{ old('assigned_employee_from_investor', isset($investor) ? $investor->assigned_employee_from_investor : false) ? 'checked' : '' }}>
    <label class="form-check-label" for="assigned_employee_from_investor">Investor will assign an employee from their side?</label>
</div>

<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $investor->notes ?? '') }}</textarea>
</div>

<button type="submit" class="btn btn-primary">Save</button>
<a href="{{ route('investors.index') }}" class="btn btn-secondary">Cancel</a>
