@extends('layouts.app')

@section('content')
{{-- Header moderne pour gestion des rôles --}}
<div class="roles-manage-header">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-gear-fill"></i>
      <div>
        <span>Gestion des Rôles et Permissions</span>
        <small>Configuration avancée des droits et accès utilisateurs</small>
      </div>
    </div>
    <div class="header-actions">
      <button class="btn-add-role" onclick="openCreateRoleModal()">
        <i class="bi bi-plus-circle"></i>
        Nouveau rôle
      </button>
      <a href="{{ route('admin.roles.supervision') }}" class="action-btn">
        <i class="bi bi-eye"></i>
        Supervision
      </a>
      <a href="{{ route('admin.dashboard') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour
      </a>
    </div>
  </div>
</div>

{{-- Messages de session --}}
@if(session('success'))
  <div class="alert alert-success alert-modern">
    <i class="bi bi-check-circle"></i>
    {{ session('success') }}
  </div>
@endif

{{-- Navigation par onglets --}}
<div class="tabs-navigation">
  <button class="tab-btn active" onclick="switchTab('roles')">
    <i class="bi bi-shield"></i>
    Rôles existants
  </button>
  <button class="tab-btn" onclick="switchTab('permissions')">
    <i class="bi bi-key"></i>
    Matrice des permissions
  </button>
  <button class="tab-btn" onclick="switchTab('audit')">
    <i class="bi bi-clock-history"></i>
    Historique des modifications
  </button>
</div>

{{-- Contenu des onglets --}}
<div class="tabs-content">
  {{-- Onglet Rôles existants --}}
  <div id="roles-tab" class="tab-panel active">
    <div class="roles-grid">
      @foreach($roles ?? ['admin', 'doctor', 'secretary', 'patient'] as $role)
        <div class="role-management-card {{ $role }}-role">
          <div class="role-header">
            <div class="role-icon">
              @switch($role)
                @case('admin')
                  <i class="bi bi-shield-fill"></i>
                  @break
                @case('doctor')
                  <i class="bi bi-person-badge"></i>
                  @break
                @case('secretary')
                  <i class="bi bi-person-workspace"></i>
                  @break
                @case('patient')
                  <i class="bi bi-person"></i>
                  @break
              @endswitch
            </div>
            <div class="role-info">
              <h3>{{ ucfirst($role) }}</h3>
              <span class="role-description">{{ $role_descriptions[$role] ?? 'Rôle système' }}</span>
              <div class="role-stats">
                <span class="user-count">{{ $role_stats[$role]['users'] ?? 0 }} utilisateurs</span>
                <span class="permissions-count">{{ $role_stats[$role]['permissions'] ?? 0 }} permissions</span>
              </div>
            </div>
            <div class="role-status {{ $role_stats[$role]['active'] ?? true ? 'active' : 'inactive' }}">
              <i class="bi bi-circle-fill"></i>
            </div>
          </div>
          
          <div class="role-body">
            <div class="permissions-preview">
              <h5>Permissions principales :</h5>
              <div class="permissions-list">
                @foreach($role_permissions[$role] ?? [] as $permission)
                  <span class="permission-badge {{ $permission['level'] }}">
                    <i class="bi {{ $permission['icon'] }}"></i>
                    {{ $permission['name'] }}
                  </span>
                @endforeach
              </div>
            </div>
            
            <div class="recent-changes">
              <div class="changes-header">
                <span>Dernière modification :</span>
                <small>{{ $role_stats[$role]['last_modified'] ?? 'Il y a 2 jours' }}</small>
              </div>
            </div>
          </div>
          
          <div class="role-actions">
            <button class="btn-edit" onclick="editRole('{{ $role }}')">
              <i class="bi bi-pencil"></i>
              Modifier
            </button>
            <button class="btn-permissions" onclick="managePermissions('{{ $role }}')">
              <i class="bi bi-key"></i>
              Permissions
            </button>
            <div class="role-menu dropdown">
              <button class="btn-menu" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots"></i>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="duplicateRole('{{ $role }}')">
                  <i class="bi bi-files"></i> Dupliquer
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportRole('{{ $role }}')">
                  <i class="bi bi-download"></i> Exporter
                </a></li>
                @if($role !== 'admin')
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger" href="#" onclick="deleteRole('{{ $role }}')">
                    <i class="bi bi-trash"></i> Supprimer
                  </a></li>
                @endif
              </ul>
            </div>
          </div>
        </div>
      @endforeach
      
      {{-- Carte pour ajouter un nouveau rôle --}}
      <div class="role-management-card add-new-role" onclick="openCreateRoleModal()">
        <div class="add-role-content">
          <div class="add-role-icon">
            <i class="bi bi-plus-circle"></i>
          </div>
          <h4>Créer un nouveau rôle</h4>
          <p>Définissez un rôle personnalisé avec des permissions spécifiques</p>
        </div>
      </div>
    </div>
  </div>
  
  {{-- Onglet Matrice des permissions --}}
  <div id="permissions-tab" class="tab-panel">
    <div class="permissions-matrix-section">
      <div class="matrix-controls">
        <div class="search-permissions">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Rechercher une permission..." id="permissionSearch">
        </div>
        <div class="filter-permissions">
          <select id="categoryFilter" class="form-select">
            <option value="">Toutes les catégories</option>
            <option value="users">Gestion des utilisateurs</option>
            <option value="patients">Gestion des patients</option>
            <option value="consultations">Consultations</option>
            <option value="payments">Paiements</option>
            <option value="reports">Rapports</option>
            <option value="system">Système</option>
          </select>
        </div>
        <button class="btn-bulk-edit" onclick="toggleBulkEdit()">
          <i class="bi bi-check2-square"></i>
          Édition en lot
        </button>
      </div>
      
      <div class="permissions-matrix">
        <div class="matrix-header">
          <div class="permission-column">Permission</div>
          <div class="role-column admin-col">Admin</div>
          <div class="role-column doctor-col">Médecin</div>
          <div class="role-column secretary-col">Secrétaire</div>
          <div class="role-column patient-col">Patient</div>
          <div class="actions-column">Actions</div>
        </div>
        
        <div class="matrix-body" id="permissionsMatrix">
          @php
            $permissions_matrix = $permissions_matrix ?? [
              'users' => [
                ['key' => 'create_user', 'name' => 'Créer un utilisateur', 'description' => 'Permet de créer de nouveaux comptes utilisateurs', 'icon' => 'bi-person-plus', 'roles' => ['admin']],
                ['key' => 'edit_user', 'name' => 'Modifier un utilisateur', 'description' => 'Permet de modifier les informations des utilisateurs', 'icon' => 'bi-person-gear', 'roles' => ['admin']],
                ['key' => 'delete_user', 'name' => 'Supprimer un utilisateur', 'description' => 'Permet de supprimer des comptes utilisateurs', 'icon' => 'bi-person-x', 'roles' => ['admin']],
                ['key' => 'view_users', 'name' => 'Voir les utilisateurs', 'description' => 'Permet de consulter la liste des utilisateurs', 'icon' => 'bi-people', 'roles' => ['admin', 'secretary']],
              ],
              'patients' => [
                ['key' => 'create_patient', 'name' => 'Créer un patient', 'description' => 'Permet d\'ajouter de nouveaux patients', 'icon' => 'bi-person-plus-fill', 'roles' => ['admin', 'secretary']],
                ['key' => 'edit_patient', 'name' => 'Modifier un patient', 'description' => 'Permet de modifier les données des patients', 'icon' => 'bi-person-gear', 'roles' => ['admin', 'doctor', 'secretary']],
                ['key' => 'view_patient', 'name' => 'Voir les patients', 'description' => 'Permet de consulter les dossiers patients', 'icon' => 'bi-eye', 'roles' => ['admin', 'doctor', 'secretary']],
                ['key' => 'delete_patient', 'name' => 'Supprimer un patient', 'description' => 'Permet de supprimer un dossier patient', 'icon' => 'bi-person-x', 'roles' => ['admin']],
              ],
              'consultations' => [
                ['key' => 'create_consultation', 'name' => 'Créer une consultation', 'description' => 'Permet de créer de nouvelles consultations', 'icon' => 'bi-calendar-plus', 'roles' => ['admin', 'doctor']],
                ['key' => 'edit_consultation', 'name' => 'Modifier une consultation', 'description' => 'Permet de modifier les consultations', 'icon' => 'bi-pencil', 'roles' => ['admin', 'doctor']],
                ['key' => 'view_consultation', 'name' => 'Voir les consultations', 'description' => 'Permet de consulter les consultations', 'icon' => 'bi-eye', 'roles' => ['admin', 'doctor', 'secretary']],
                ['key' => 'delete_consultation', 'name' => 'Supprimer une consultation', 'description' => 'Permet de supprimer des consultations', 'icon' => 'bi-trash', 'roles' => ['admin', 'doctor']],
              ],
              'payments' => [
                ['key' => 'view_payments', 'name' => 'Voir les paiements', 'description' => 'Permet de consulter les paiements', 'icon' => 'bi-credit-card', 'roles' => ['admin', 'secretary']],
                ['key' => 'manage_payments', 'name' => 'Gérer les paiements', 'description' => 'Permet de gérer les transactions', 'icon' => 'bi-cash-coin', 'roles' => ['admin', 'secretary']],
                ['key' => 'refund_payments', 'name' => 'Rembourser les paiements', 'description' => 'Permet d\'effectuer des remboursements', 'icon' => 'bi-arrow-counterclockwise', 'roles' => ['admin']],
              ],
              'system' => [
                ['key' => 'manage_roles', 'name' => 'Gérer les rôles', 'description' => 'Permet de modifier les rôles et permissions', 'icon' => 'bi-shield', 'roles' => ['admin']],
                ['key' => 'system_settings', 'name' => 'Paramètres système', 'description' => 'Permet de modifier les paramètres du système', 'icon' => 'bi-gear', 'roles' => ['admin']],
                ['key' => 'view_logs', 'name' => 'Voir les logs', 'description' => 'Permet de consulter les journaux système', 'icon' => 'bi-file-text', 'roles' => ['admin']],
              ]
            ];
            
            $audit_logs = $audit_logs ?? [
              ['id' => '1', 'time' => '14:32', 'date' => 'Aujourd\'hui', 'user' => 'Admin', 'action_text' => 'a modifié', 'target' => 'le rôle Médecin', 'details' => 'Ajout de la permission "créer_ordonnance"', 'icon' => 'bi-pencil', 'action' => 'update', 'ip' => '192.168.1.10'],
              ['id' => '2', 'time' => '13:15', 'date' => 'Aujourd\'hui', 'user' => 'Dr. Martin', 'action_text' => 'a créé', 'target' => 'une nouvelle consultation', 'details' => 'Consultation pour le patient #1234', 'icon' => 'bi-plus-circle', 'action' => 'create', 'ip' => '192.168.1.25'],
              ['id' => '3', 'time' => '11:48', 'date' => 'Hier', 'user' => 'Secrétaire1', 'action_text' => 'a supprimé', 'target' => 'un rendez-vous', 'details' => 'Annulation du RDV du 15/10 à 14h00', 'icon' => 'bi-trash', 'action' => 'delete', 'ip' => '192.168.1.30'],
              ['id' => '4', 'time' => '10:22', 'date' => 'Hier', 'user' => 'Admin', 'action_text' => 'a changé les permissions de', 'target' => 'Secrétaire', 'details' => 'Retrait de l\'accès aux rapports financiers', 'icon' => 'bi-key', 'action' => 'permission_change', 'ip' => '192.168.1.10', 'changes' => [['field' => 'financial_reports', 'old' => 'true', 'new' => 'false']]],
              ['id' => '5', 'time' => '16:05', 'date' => '12 Oct', 'user' => 'Dr. Dupont', 'action_text' => 'a créé', 'target' => 'une ordonnance', 'details' => 'Prescription pour le patient Marie Dubois', 'icon' => 'bi-prescription2', 'action' => 'create', 'ip' => '192.168.1.28']
            ];
          @endphp
          @foreach($permissions_matrix as $category => $permissions)
            <div class="category-header">
              <h5>
                <i class="bi bi-chevron-down"></i>
                {{ ucfirst($category) }}
                <span class="category-count">{{ count($permissions) }} permissions</span>
              </h5>
            </div>
            
            @foreach($permissions as $permission)
              <div class="permission-row" data-category="{{ $category }}">
                <div class="permission-info">
                  <div class="permission-name">
                    <i class="bi {{ $permission['icon'] }}"></i>
                    {{ $permission['name'] }}
                  </div>
                  <div class="permission-description">{{ $permission['description'] }}</div>
                </div>
                
                <div class="permission-checkbox admin-col">
                  <label class="checkbox-wrapper">
                    <input type="checkbox" {{ in_array('admin', $permission['roles']) ? 'checked' : '' }} 
                           onchange="togglePermission('{{ $permission['key'] }}', 'admin', this.checked)">
                    <span class="checkmark"></span>
                  </label>
                </div>
                
                <div class="permission-checkbox doctor-col">
                  <label class="checkbox-wrapper">
                    <input type="checkbox" {{ in_array('doctor', $permission['roles']) ? 'checked' : '' }}
                           onchange="togglePermission('{{ $permission['key'] }}', 'doctor', this.checked)">
                    <span class="checkmark"></span>
                  </label>
                </div>
                
                <div class="permission-checkbox secretary-col">
                  <label class="checkbox-wrapper">
                    <input type="checkbox" {{ in_array('secretary', $permission['roles']) ? 'checked' : '' }}
                           onchange="togglePermission('{{ $permission['key'] }}', 'secretary', this.checked)">
                    <span class="checkmark"></span>
                  </label>
                </div>
                
                <div class="permission-checkbox patient-col">
                  <label class="checkbox-wrapper">
                    <input type="checkbox" {{ in_array('patient', $permission['roles']) ? 'checked' : '' }}
                           onchange="togglePermission('{{ $permission['key'] }}', 'patient', this.checked)">
                    <span class="checkmark"></span>
                  </label>
                </div>
                
                <div class="permission-actions">
                  <button class="btn-edit-permission" onclick="editPermission('{{ $permission['key'] }}')">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-info-permission" onclick="showPermissionInfo('{{ $permission['key'] }}')">
                    <i class="bi bi-info-circle"></i>
                  </button>
                </div>
              </div>
            @endforeach
          @endforeach
        </div>
      </div>
      
      <div class="matrix-actions">
        <button class="btn-save-changes" onclick="savePermissionChanges()">
          <i class="bi bi-check-circle"></i>
          Sauvegarder les modifications
        </button>
        <button class="btn-reset-changes" onclick="resetPermissionChanges()">
          <i class="bi bi-arrow-counterclockwise"></i>
          Annuler les changements
        </button>
      </div>
    </div>
  </div>
  
  {{-- Onglet Historique --}}
  <div id="audit-tab" class="tab-panel">
    <div class="audit-section">
      <div class="audit-filters">
        <div class="filter-group">
          <label>Période :</label>
          <select class="form-select" id="auditPeriod">
            <option value="today">Aujourd'hui</option>
            <option value="week" selected>Cette semaine</option>
            <option value="month">Ce mois</option>
            <option value="quarter">Ce trimestre</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Type d'action :</label>
          <select class="form-select" id="auditAction">
            <option value="">Toutes les actions</option>
            <option value="create">Création</option>
            <option value="update">Modification</option>
            <option value="delete">Suppression</option>
            <option value="permission_change">Changement de permission</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Utilisateur :</label>
          <input type="text" class="form-control" placeholder="Rechercher un utilisateur..." id="auditUser">
        </div>
      </div>
      
      <div class="audit-timeline">
        @foreach($audit_logs ?? [] as $log)
          <div class="audit-item {{ $log['action'] }}">
            <div class="audit-timestamp">
              <div class="time">{{ $log['time'] }}</div>
              <div class="date">{{ $log['date'] }}</div>
            </div>
            <div class="audit-icon">
              <i class="bi {{ $log['icon'] }}"></i>
            </div>
            <div class="audit-content">
              <div class="audit-header">
                <span class="audit-user">{{ $log['user'] }}</span>
                <span class="audit-action">{{ $log['action_text'] }}</span>
                <span class="audit-target">{{ $log['target'] }}</span>
              </div>
              <div class="audit-details">{{ $log['details'] }}</div>
              @if(isset($log['changes']))
                <div class="audit-changes">
                  <strong>Changements :</strong>
                  @foreach($log['changes'] as $change)
                    <span class="change-item">{{ $change['field'] }}: {{ $change['old'] }} → {{ $change['new'] }}</span>
                  @endforeach
                </div>
              @endif
            </div>
            <div class="audit-meta">
              <span class="audit-ip">IP: {{ $log['ip'] ?? '192.168.1.1' }}</span>
              <button class="btn-audit-details" onclick="showAuditDetails('{{ $log['id'] }}')">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        @endforeach
      </div>
      
      <div class="audit-pagination">
        <button class="btn-load-more" onclick="loadMoreAuditLogs()">
          <i class="bi bi-arrow-down-circle"></i>
          Charger plus d'entrées
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal pour créer/modifier un rôle --}}
<div class="modal fade" id="roleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roleModalTitle">Créer un nouveau rôle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="roleForm">
          <div class="role-basic-info">
            <div class="mb-3">
              <label class="form-label">Nom du rôle *</label>
              <input type="text" class="form-control" id="roleName" placeholder="ex: Infirmier, Technicien..." required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control" id="roleDescription" rows="3" placeholder="Décrivez les responsabilités de ce rôle..."></textarea>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Icône</label>
              <div class="icon-selector">
                <div class="selected-icon" onclick="toggleIconPicker()">
                  <i class="bi bi-person" id="selectedIcon"></i>
                  <span>Choisir une icône</span>
                </div>
                <div class="icon-picker" id="iconPicker" style="display: none;">
                  <div class="icon-option" data-icon="bi-person"><i class="bi bi-person"></i></div>
                  <div class="icon-option" data-icon="bi-person-badge"><i class="bi bi-person-badge"></i></div>
                  <div class="icon-option" data-icon="bi-person-workspace"><i class="bi bi-person-workspace"></i></div>
                  <div class="icon-option" data-icon="bi-person-gear"><i class="bi bi-person-gear"></i></div>
                  <div class="icon-option" data-icon="bi-clipboard-pulse"><i class="bi bi-clipboard-pulse"></i></div>
                  <div class="icon-option" data-icon="bi-stethoscope"><i class="bi bi-stethoscope"></i></div>
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Couleur du thème</label>
              <div class="color-selector">
                <div class="color-option" data-color="#3b82f6" style="background: #3b82f6;"></div>
                <div class="color-option" data-color="#059669" style="background: #059669;"></div>
                <div class="color-option" data-color="#8b5cf6" style="background: #8b5cf6;"></div>
                <div class="color-option" data-color="#f59e0b" style="background: #f59e0b;"></div>
                <div class="color-option" data-color="#dc2626" style="background: #dc2626;"></div>
                <div class="color-option" data-color="#6b7280" style="background: #6b7280;"></div>
              </div>
            </div>
          </div>
          
          <div class="role-permissions-config">
            <h6>Permissions de base</h6>
            <div class="permission-categories">
              <div class="category-section">
                <h6>Authentification & Profil</h6>
                <div class="permission-checkboxes">
                  <label><input type="checkbox" checked disabled> Se connecter</label>
                  <label><input type="checkbox" checked disabled> Modifier son profil</label>
                  <label><input type="checkbox"> Changer son mot de passe</label>
                </div>
              </div>
              
              <div class="category-section">
                <h6>Patients</h6>
                <div class="permission-checkboxes">
                  <label><input type="checkbox"> Voir les patients</label>
                  <label><input type="checkbox"> Créer un patient</label>
                  <label><input type="checkbox"> Modifier un patient</label>
                  <label><input type="checkbox"> Supprimer un patient</label>
                </div>
              </div>
              
              <div class="category-section">
                <h6>Consultations</h6>
                <div class="permission-checkboxes">
                  <label><input type="checkbox"> Voir les consultations</label>
                  <label><input type="checkbox"> Créer une consultation</label>
                  <label><input type="checkbox"> Modifier une consultation</label>
                  <label><input type="checkbox"> Supprimer une consultation</label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" onclick="saveRole()">
          <i class="bi bi-check-circle"></i>
          Créer le rôle
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  /* Variables CSS */
  :root {
    --primary-color: #3b82f6;
    --success-color: #059669;
    --warning-color: #f59e0b;
    --danger-color: #dc2626;
    --purple-color: #8b5cf6;
    --admin-color: #dc2626;
    --doctor-color: #059669;
    --secretary-color: #8b5cf6;
    --patient-color: #3b82f6;
  }
  
  /* Conteneur principal */
  body > .container { max-width: 1600px !important; }
  
  /* Header gestion rôles */
  .roles-manage-header {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(5, 150, 105, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .header-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.75rem;
    border-radius: 12px;
    font-size: 1.5rem;
  }
  
  .header-title span {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  
  .header-title small {
    font-size: 1rem;
    opacity: 0.9;
  }
  
  .header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .btn-add-role, .action-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-add-role:hover, .action-btn:hover {
    background: white;
    color: #059669;
    transform: translateY(-2px);
  }
  
  /* Alertes modernes */
  .alert-modern {
    border-radius: 12px;
    padding: 1rem 1.5rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
  }
  
  /* Navigation par onglets */
  .tabs-navigation {
    background: white;
    border-radius: 16px;
    padding: 0.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    gap: 0.5rem;
  }
  
  .tab-btn {
    flex: 1;
    background: none;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 500;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .tab-btn:hover {
    background: #f3f4f6;
    color: #374151;
  }
  
  .tab-btn.active {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
  }
  
  /* Contenu des onglets */
  .tabs-content {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }
  
  .tab-panel {
    display: none;
  }
  
  .tab-panel.active {
    display: block;
  }
  
  /* Grille des rôles */
  .roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 2rem;
  }
  
  .role-management-card {
    background: #f8fafc;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }
  
  .role-management-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }
  
  .admin-role:hover { border-color: var(--admin-color); }
  .doctor-role:hover { border-color: var(--doctor-color); }
  .secretary-role:hover { border-color: var(--secretary-color); }
  .patient-role:hover { border-color: var(--patient-color); }
  
  .role-header {
    background: white;
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .role-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
  }
  
  .admin-role .role-icon { background: linear-gradient(135deg, var(--admin-color), #b91c1c); }
  .doctor-role .role-icon { background: linear-gradient(135deg, var(--doctor-color), #047857); }
  .secretary-role .role-icon { background: linear-gradient(135deg, var(--secretary-color), #7c3aed); }
  .patient-role .role-icon { background: linear-gradient(135deg, var(--patient-color), #1d4ed8); }
  
  .role-info {
    flex: 1;
  }
  
  .role-info h3 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-weight: 600;
  }
  
  .role-description {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
    display: block;
  }
  
  .role-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: #9ca3af;
  }
  
  .role-status {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #f0fdf4;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #16a34a;
    flex-shrink: 0;
  }
  
  .role-status.inactive {
    background: #fef2f2;
    color: #dc2626;
  }
  
  .role-body {
    padding: 1.5rem;
  }
  
  .permissions-preview h5 {
    margin: 0 0 1rem 0;
    color: #374151;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  .permissions-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }
  
  .permission-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .permission-badge.high {
    background: #fee2e2;
    color: #dc2626;
  }
  
  .permission-badge.medium {
    background: #fef3c7;
    color: #d97706;
  }
  
  .permission-badge.low {
    background: #dcfce7;
    color: #059669;
  }
  
  .recent-changes {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: #6b7280;
  }
  
  .role-actions {
    padding: 1rem 1.5rem;
    background: white;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .btn-edit, .btn-permissions {
    flex: 1;
    padding: 0.6rem;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-edit {
    background: #f3f4f6;
    color: #374151;
  }
  
  .btn-edit:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
  }
  
  .btn-permissions {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
  }
  
  .btn-permissions:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
  }
  
  .btn-menu {
    background: none;
    border: none;
    color: #6b7280;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
  }
  
  .btn-menu:hover {
    background: #f3f4f6;
    color: #374151;
  }
  
  /* Carte d'ajout de rôle */
  .add-new-role {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    border: 2px dashed #d1d5db;
    background: #f9fafb;
    cursor: pointer;
  }
  
  .add-new-role:hover {
    border-color: #059669;
    background: #f0fdf4;
  }
  
  .add-role-content {
    text-align: center;
    color: #6b7280;
  }
  
  .add-role-icon {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
  }
  
  .add-new-role:hover .add-role-icon {
    color: #059669;
  }
  
  .add-role-content h4 {
    color: #374151;
    margin-bottom: 0.5rem;
  }
  
  /* Matrice des permissions */
  .permissions-matrix-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .matrix-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
  }
  
  .search-permissions {
    position: relative;
    flex: 1;
    min-width: 250px;
  }
  
  .search-permissions i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
  }
  
  .search-permissions input {
    padding-left: 2.5rem;
    border-radius: 10px;
    border: 2px solid #e5e7eb;
  }
  
  .filter-permissions select {
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    min-width: 200px;
  }
  
  .btn-bulk-edit {
    background: #f3f4f6;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .btn-bulk-edit:hover {
    background: #e5e7eb;
  }
  
  .permissions-matrix {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .matrix-header {
    background: #f8fafc;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr 100px;
    gap: 1rem;
    padding: 1rem 1.5rem;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
  }
  
  .matrix-body {
    max-height: 600px;
    overflow-y: auto;
  }
  
  .category-header {
    background: #f9fafb;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
  }
  
  .category-header h5 {
    margin: 0;
    color: #1f2937;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
  }
  
  .category-count {
    color: #9ca3af;
    font-size: 0.8rem;
    font-weight: normal;
  }
  
  .permission-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr 100px;
    gap: 1rem;
    padding: 1rem 1.5rem;
    align-items: center;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
  }
  
  .permission-row:hover {
    background: #f9fafb;
  }
  
  .permission-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .permission-name {
    font-weight: 500;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .permission-description {
    font-size: 0.8rem;
    color: #6b7280;
  }
  
  .permission-checkbox {
    display: flex;
    justify-content: center;
  }
  
  .checkbox-wrapper {
    position: relative;
    cursor: pointer;
  }
  
  .checkbox-wrapper input {
    display: none;
  }
  
  .checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    display: block;
    position: relative;
    transition: all 0.2s ease;
  }
  
  .checkbox-wrapper input:checked + .checkmark {
    background: #059669;
    border-color: #059669;
  }
  
  .checkbox-wrapper input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    color: white;
    font-weight: bold;
    font-size: 12px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  
  .permission-actions {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
  }
  
  .btn-edit-permission, .btn-info-permission {
    background: none;
    border: none;
    color: #6b7280;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
  }
  
  .btn-edit-permission:hover {
    background: #f3f4f6;
    color: #374151;
  }
  
  .btn-info-permission:hover {
    background: #eff6ff;
    color: #3b82f6;
  }
  
  .matrix-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    padding: 2rem 0;
  }
  
  .btn-save-changes, .btn-reset-changes {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .btn-save-changes {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
  }
  
  .btn-save-changes:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
  }
  
  .btn-reset-changes {
    background: #f3f4f6;
    color: #374151;
  }
  
  .btn-reset-changes:hover {
    background: #e5e7eb;
  }
  
  /* Section audit */
  .audit-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .audit-filters {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
  }
  
  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .filter-group label {
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
  }
  
  .audit-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .audit-item {
    display: grid;
    grid-template-columns: 120px 50px 1fr 150px;
    gap: 1rem;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 12px;
    border-left: 4px solid;
    align-items: start;
  }
  
  .audit-item.create { border-color: #059669; }
  .audit-item.update { border-color: #3b82f6; }
  .audit-item.delete { border-color: #dc2626; }
  .audit-item.permission_change { border-color: #8b5cf6; }
  
  .audit-timestamp {
    text-align: center;
  }
  
  .audit-timestamp .time {
    font-weight: 600;
    color: #1f2937;
  }
  
  .audit-timestamp .date {
    font-size: 0.8rem;
    color: #6b7280;
  }
  
  .audit-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
  }
  
  .audit-item.create .audit-icon { background: #059669; }
  .audit-item.update .audit-icon { background: #3b82f6; }
  .audit-item.delete .audit-icon { background: #dc2626; }
  .audit-item.permission_change .audit-icon { background: #8b5cf6; }
  
  .audit-content {
    flex: 1;
  }
  
  .audit-header {
    margin-bottom: 0.5rem;
  }
  
  .audit-user {
    font-weight: 600;
    color: #1f2937;
  }
  
  .audit-action {
    color: #6b7280;
  }
  
  .audit-target {
    font-weight: 500;
    color: #374151;
  }
  
  .audit-details {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
  }
  
  .audit-changes {
    font-size: 0.8rem;
    color: #6b7280;
  }
  
  .change-item {
    display: inline-block;
    margin-right: 1rem;
    padding: 0.25rem 0.5rem;
    background: white;
    border-radius: 4px;
    font-family: monospace;
  }
  
  .audit-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
  }
  
  .audit-ip {
    font-size: 0.8rem;
    color: #9ca3af;
    font-family: monospace;
  }
  
  .btn-audit-details {
    background: none;
    border: none;
    color: #6b7280;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
  }
  
  .btn-audit-details:hover {
    background: #eff6ff;
    color: #3b82f6;
  }
  
  .btn-load-more {
    background: #f3f4f6;
    border: none;
    padding: 1rem 2rem;
    border-radius: 10px;
    font-weight: 500;
    color: #374151;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin: 0 auto;
    transition: all 0.2s ease;
  }
  
  .btn-load-more:hover {
    background: #e5e7eb;
  }
  
  /* Boutons d'export */
  .btn-export-matrix, .btn-export-audit {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.9rem;
  }
  
  .btn-export-matrix:hover, .btn-export-audit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
  }
  
  .btn-export-matrix:active, .btn-export-audit:active {
    transform: translateY(0);
  }
  
  /* Modal pour rôles */
  .role-basic-info .form-control {
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    padding: 0.75rem;
  }
  
  .role-basic-info .form-control:focus {
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
  }
  
  .icon-selector {
    position: relative;
  }
  
  .selected-icon {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .selected-icon:hover {
    border-color: #059669;
  }
  
  .selected-icon i {
    font-size: 1.2rem;
    color: #6b7280;
  }
  
  .icon-picker {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.5rem;
    z-index: 10;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }
  
  .icon-option {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
  }
  
  .icon-option:hover {
    background: #f3f4f6;
    border-color: #059669;
  }
  
  .icon-option i {
    font-size: 1.2rem;
    color: #6b7280;
  }
  
  .color-selector {
    display: flex;
    gap: 0.5rem;
  }
  
  .color-option {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.2s ease;
  }
  
  .color-option:hover, .color-option.selected {
    border-color: white;
    box-shadow: 0 0 0 2px #374151;
    transform: scale(1.1);
  }
  
  .permission-categories {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-top: 1rem;
  }
  
  .category-section h6 {
    margin: 0 0 0.75rem 0;
    color: #374151;
    font-weight: 600;
  }
  
  .permission-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
  }
  
  .permission-checkboxes label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .permission-checkboxes label:hover {
    background: #f9fafb;
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .roles-grid {
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    }
    
    .matrix-header, .permission-row {
      grid-template-columns: 2fr 80px 80px 80px 80px 60px;
    }
  }
  
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
      gap: 1rem;
    }
    
    .header-actions {
      flex-wrap: wrap;
      justify-content: center;
    }
    
    .tabs-navigation {
      flex-direction: column;
    }
    
    .roles-grid {
      grid-template-columns: 1fr;
    }
    
    .matrix-controls {
      flex-direction: column;
      align-items: stretch;
    }
    
    .permissions-matrix {
      overflow-x: auto;
    }
    
    .audit-filters {
      flex-direction: column;
    }
    
    .audit-item {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
  }
</style>

<script>
  // Variables globales
  let currentTab = 'roles';
  let selectedRole = null;
  let permissionChanges = {};
  
  // Gestion des onglets
  function switchTab(tabName) {
    // Mise à jour des boutons
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`button[onclick="switchTab('${tabName}')"]`).classList.add('active');
    
    // Mise à jour des panneaux
    document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
    document.getElementById(`${tabName}-tab`).classList.add('active');
    
    currentTab = tabName;
  }
  
  // Gestion des rôles
  function openCreateRoleModal() {
    document.getElementById('roleModalTitle').textContent = 'Créer un nouveau rôle';
    document.getElementById('roleForm').reset();
    new bootstrap.Modal(document.getElementById('roleModal')).show();
  }
  
  function editRole(role) {
    selectedRole = role;
    document.getElementById('roleModalTitle').textContent = `Modifier le rôle ${role}`;
    // Pré-remplir le formulaire avec les données existantes
    document.getElementById('roleName').value = role;
    new bootstrap.Modal(document.getElementById('roleModal')).show();
  }
  
  function duplicateRole(role) {
    if(confirm(`Voulez-vous dupliquer le rôle ${role} ?`)) {
      alert(`Le rôle ${role} a été dupliqué avec succès`);
    }
  }
  
  function exportRole(role) {
    // Créer les données d'export
    const roleData = {
      role: role,
      timestamp: new Date().toISOString(),
      permissions: getRolePermissions(role),
      users: getRoleUsers(role),
      metadata: {
        exportedBy: 'Admin User',
        version: '1.0',
        system: 'SMART-HEALTH'
      }
    };
    
    // Exporter en JSON
    exportToJSON(roleData, `role-${role}-${new Date().toISOString().split('T')[0]}.json`);
    
    // Log de l'audit
    logAuditAction('export', `Rôle ${role}`, `Export des données du rôle ${role}`);
  }
  
  function deleteRole(role) {
    if(confirm(`Êtes-vous sûr de vouloir supprimer le rôle ${role} ? Cette action est irréversible.`)) {
      alert(`Le rôle ${role} a été supprimé`);
    }
  }
  
  function saveRole() {
    const name = document.getElementById('roleName').value;
    if(!name.trim()) {
      alert('Veuillez saisir un nom pour le rôle');
      return;
    }
    
    alert(`Le rôle "${name}" a été ${selectedRole ? 'modifié' : 'créé'} avec succès`);
    bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
    selectedRole = null;
  }
  
  function managePermissions(role) {
    switchTab('permissions');
    // Faire défiler jusqu'à la matrice des permissions
    document.getElementById('permissions-tab').scrollIntoView({ behavior: 'smooth' });
  }
  
  // Gestion des permissions
  function togglePermission(permissionKey, role, checked) {
    if(!permissionChanges[permissionKey]) {
      permissionChanges[permissionKey] = {};
    }
    permissionChanges[permissionKey][role] = checked;
    
    // Afficher un indicateur de changement
    console.log(`Permission ${permissionKey} pour ${role}: ${checked}`);
  }
  
  function editPermission(permissionKey) {
    alert(`Édition de la permission ${permissionKey}`);
  }
  
  function showPermissionInfo(permissionKey) {
    alert(`Informations sur la permission ${permissionKey}`);
  }
  
  function toggleBulkEdit() {
    alert('Mode d\'édition en lot activé');
  }
  
  function savePermissionChanges() {
    if(Object.keys(permissionChanges).length === 0) {
      alert('Aucune modification à sauvegarder');
      return;
    }
    
    if(confirm('Sauvegarder toutes les modifications ?')) {
      console.log('Changements sauvegardés:', permissionChanges);
      alert('Permissions mises à jour avec succès');
      permissionChanges = {};
    }
  }
  
  function resetPermissionChanges() {
    if(confirm('Annuler tous les changements non sauvegardés ?')) {
      permissionChanges = {};
      location.reload();
    }
  }
  
  // Gestion des filtres de permissions
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('permissionSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if(searchInput) {
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.permission-row');
        
        rows.forEach(row => {
          const permissionName = row.querySelector('.permission-name').textContent.toLowerCase();
          const permissionDesc = row.querySelector('.permission-description').textContent.toLowerCase();
          
          if(permissionName.includes(searchTerm) || permissionDesc.includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
    
    if(categoryFilter) {
      categoryFilter.addEventListener('change', function() {
        const selectedCategory = this.value;
        const rows = document.querySelectorAll('.permission-row');
        const headers = document.querySelectorAll('.category-header');
        
        if(selectedCategory === '') {
          rows.forEach(row => row.style.display = '');
          headers.forEach(header => header.style.display = '');
        } else {
          rows.forEach(row => {
            if(row.dataset.category === selectedCategory) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
          
          headers.forEach(header => {
            const categoryName = header.querySelector('h5').textContent.toLowerCase();
            if(categoryName.includes(selectedCategory)) {
              header.style.display = '';
            } else {
              header.style.display = 'none';
            }
          });
        }
      });
    }
  });
  
  // Gestion de l'audit
  function showAuditDetails(logId) {
    alert(`Détails de l'entrée d'audit ${logId}`);
  }
  
  function loadMoreAuditLogs() {
    alert('Chargement de plus d\'entrées...');
  }
  
  // Gestion du sélecteur d'icônes
  function toggleIconPicker() {
    const picker = document.getElementById('iconPicker');
    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
  }
  
  // Initialisation
  document.addEventListener('DOMContentLoaded', function() {
    // Gestion des options d'icônes
    document.querySelectorAll('.icon-option').forEach(option => {
      option.addEventListener('click', function() {
        const iconClass = this.dataset.icon;
        document.getElementById('selectedIcon').className = `bi ${iconClass}`;
        document.getElementById('iconPicker').style.display = 'none';
      });
    });
    
    // Gestion des couleurs
    document.querySelectorAll('.color-option').forEach(option => {
      option.addEventListener('click', function() {
        document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');
      });
    });
    
  // Fermer le sélecteur d'icônes en cliquant ailleurs
    document.addEventListener('click', function(e) {
      const iconSelector = document.querySelector('.icon-selector');
      const iconPicker = document.getElementById('iconPicker');
      
      if(iconSelector && !iconSelector.contains(e.target) && iconPicker) {
        iconPicker.style.display = 'none';
      }
    });
  });
  
  // ============= FONCTIONS D'EXPORT ============= //
  
  function exportToJSON(data, filename) {
    const jsonString = JSON.stringify(data, null, 2);
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    showNotification('success', `Fichier ${filename} téléchargé avec succès`);
  }
  
  function exportToCSV(data, filename) {
    const csvContent = convertToCSV(data);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    showNotification('success', `Rapport CSV ${filename} exporté`);
  }
  
  function convertToCSV(data) {
    const array = Array.isArray(data) ? data : [data];
    if (array.length === 0) return '';
    
    const headers = Object.keys(array[0]);
    const csvHeaders = headers.join(',');
    
    const csvRows = array.map(row => 
      headers.map(header => {
        const value = row[header];
        return typeof value === 'string' && value.includes(',') ? `"${value}"` : value;
      }).join(',')
    );
    
    return [csvHeaders, ...csvRows].join('\n');
  }
  
  function exportPermissionsMatrix() {
    // Utiliser les vraies données de permissions depuis PHP
    const permissionsMatrix = @json($permissions_matrix);
    const matrixData = [];
    
    Object.keys(permissionsMatrix).forEach(category => {
      permissionsMatrix[category].forEach(permission => {
        const rowData = {
          categorie: category,
          permission: permission.name,
          cle: permission.key,
          description: permission.description,
          icone: permission.icon,
          admin: permission.roles.includes('admin') ? 'Oui' : 'Non',
          doctor: permission.roles.includes('doctor') ? 'Oui' : 'Non',
          secretary: permission.roles.includes('secretary') ? 'Oui' : 'Non',
          patient: permission.roles.includes('patient') ? 'Oui' : 'Non'
        };
        
        matrixData.push(rowData);
      });
    });
    
    const filename = `permissions-matrix-${new Date().toISOString().split('T')[0]}.csv`;
    exportToCSV(matrixData, filename);
    
    logAuditAction('export', 'Matrice des permissions', `Export de ${matrixData.length} permissions organisées en ${Object.keys(permissionsMatrix).length} catégories`);
  }
  
  function exportAuditReport() {
    // Utiliser les vraies données d'audit depuis PHP
    const auditLogs = getAuditLogs();
    const auditData = auditLogs.map(log => ({
      id: log.id,
      date: log.date,
      heure: log.time,
      utilisateur: log.user,
      action: `${log.action_text} ${log.target}`,
      details: log.details,
      type_action: log.action,
      adresse_ip: log.ip,
      changes: log.changes ? JSON.stringify(log.changes) : ''
    }));
    
    const filename = `audit-report-${new Date().toISOString().split('T')[0]}.csv`;
    exportToCSV(auditData, filename);
    
    logAuditAction('export', 'Rapport d\'audit', `Export de ${auditData.length} entrées d\'audit`);
  }
  
  // ============= FONCTIONS D'AIDE ============= //
  
  function getPermissionsData() {
    // Récupérer les données depuis les variables PHP
    const permissions = @json($permissions_matrix);
    const roles = ['admin', 'doctor', 'secretary', 'patient'];
    
    const matrix = {};
    
    Object.keys(permissions).forEach(category => {
      matrix[category] = {};
      permissions[category].forEach(permission => {
        matrix[category][permission.key] = {};
        roles.forEach(role => {
          matrix[category][permission.key][role] = permission.roles.includes(role);
        });
      });
    });
    
    return matrix;
  }
  
  function getRolePermissions(role) {
    const permissionsMatrix = @json($permissions_matrix);
    const rolePermissions = [];
    
    Object.keys(permissionsMatrix).forEach(category => {
      permissionsMatrix[category].forEach(permission => {
        if (permission.roles.includes(role)) {
          rolePermissions.push(permission.key);
        }
      });
    });
    
    return rolePermissions;
  }
  
  function getRoleUsers(role) {
    // Simuler la récupération des utilisateurs d'un rôle avec des données réalistes
    const users = {
      admin: ['admin@smart-health.com', 'dr.chef@smart-health.com'],
      doctor: ['dr.martin@smart-health.com', 'dr.dupont@smart-health.com', 'dr.bernard@smart-health.com'],
      secretary: ['secretaire.reception@smart-health.com', 'secretaire.medical@smart-health.com'],
      patient: [] // Trop nombreux pour être listés
    };
    return users[role] || [];
  }
  
  function getAuditLogs() {
    return @json($audit_logs);
  }
  
  function logAuditAction(action, target, details) {
    const auditLog = {
      timestamp: new Date().toISOString(),
      user: 'Admin User', // Récupérer l'utilisateur actuel
      action: action,
      target: target,
      details: details,
      ip: 'XXX.XXX.XXX.XXX', // IP de l'utilisateur
      userAgent: navigator.userAgent
    };
    
    // Envoyer vers le serveur ou stocker localement
    console.log('Audit Log:', auditLog);
    
    // Dans un vrai système, envoyer via AJAX:
    // fetch('/admin/audit/log', { method: 'POST', body: JSON.stringify(auditLog) });
  }
  
  function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-modern position-fixed`;
    notification.style.cssText = `
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
      animation: slideInRight 0.3s ease;
    `;
    notification.innerHTML = `
      <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
      ${message}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }
  
  // Ajouter les animations CSS pour les notifications
  const notificationStyle = document.createElement('style');
  notificationStyle.textContent = `
    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(100px); }
      to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOutRight {
      from { opacity: 1; transform: translateX(0); }
      to { opacity: 0; transform: translateX(100px); }
    }
  `;
  document.head.appendChild(notificationStyle);
  
  // ============= BOUTONS D'EXPORT DANS L'UI ============= //
  
  // Ajouter des boutons d'export dans les onglets
  document.addEventListener('DOMContentLoaded', function() {
    // Bouton d'export pour la matrice des permissions
    const permissionsTab = document.getElementById('permissions-tab');
    if (permissionsTab) {
      const exportBtn = document.createElement('button');
      exportBtn.className = 'btn-export-matrix';
      exportBtn.innerHTML = '<i class="bi bi-download"></i> Exporter la matrice';
      exportBtn.onclick = exportPermissionsMatrix;
      
      const matrixControls = permissionsTab.querySelector('.matrix-controls');
      if (matrixControls) {
        matrixControls.appendChild(exportBtn);
      }
    }
    
    // Bouton d'export pour l'audit
    const auditTab = document.getElementById('audit-tab');
    if (auditTab) {
      const exportBtn = document.createElement('button');
      exportBtn.className = 'btn-export-audit';
      exportBtn.innerHTML = '<i class="bi bi-file-earmark-spreadsheet"></i> Exporter le rapport';
      exportBtn.onclick = exportAuditReport;
      
      const auditFilters = auditTab.querySelector('.audit-filters');
      if (auditFilters) {
        auditFilters.appendChild(exportBtn);
      }
    }
  });
  
</script>
@endsection
