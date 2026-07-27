<div class="card">
    <div class="card-header"><h5>Mandataire</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Nom du mandataire *</label>
                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $customer->customer_name ?? '') }}" required maxlength="100">
            </div>
            <div class="col-md-6 form-group">
                <label>Contact du mandataire *</label>
                <input type="text" name="customer_contact" class="form-control" value="{{ old('customer_contact', $customer->customer_contact ?? '') }}" required maxlength="40">
            </div>
            <div class="col-md-6 form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}" maxlength="40">
            </div>
            <div class="col-md-6 form-group">
                <label>Adresse</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address ?? '') }}" maxlength="255">
            </div>
            <div class="col-md-6 form-group">
                <label>Ville</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}" maxlength="255">
            </div>
            <div class="col-md-6 form-group">
                <label>Pays</label>
                <input type="text" name="country" class="form-control" value="{{ old('country', $customer->country ?? '') }}" maxlength="255">
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h5>Agent référent</h5><small class="text-muted">La personne de contact chez le mandataire, si différente.</small></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Nom de l'agent *</label>
                <input type="text" name="agent_name" class="form-control" value="{{ old('agent_name', $customer->agent_name ?? '') }}" required maxlength="100">
            </div>
            <div class="col-md-6 form-group">
                <label>Contact de l'agent *</label>
                <input type="text" name="agent_contact" class="form-control" value="{{ old('agent_contact', $customer->agent_contact ?? '') }}" required maxlength="100">
            </div>
        </div>
    </div>
</div>
