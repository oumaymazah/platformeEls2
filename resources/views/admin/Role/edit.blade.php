
<div class="card shadow-sm">
    <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-user-shield me-2"></i>Modifier le Rôle</h5>

        </div>
    </div>
    <div class="card-body p-4">
        <form id="edit-role-form" action="{{ route('admin.roles.update', $role) }}" class="needs-validation" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-12 mb-4">
                    <label for="roleName" class="form-label fw-medium">Nom du rôle</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
                        <input class="form-control" type="text" id="roleName" name="name" required value="{{ $role->name }}" autocomplete="off" placeholder="Entrez le nom du rôle">
                        <div class="invalid-feedback">
                            Veuillez saisir un nom pour ce rôle.
                        </div>
                    </div>

                </div>


            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">
                    <i class="fas fa-times-circle me-2"></i>Annuler
                </button>
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<style>


    .bg-gradient-to-r {
        background: linear-gradient(to right, #4361ee, #3f51b5);
    }


</style>
