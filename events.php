<?php
session_start();
require 'Asset/php/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event = null;

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header("Location: events.php");
        exit();
    }
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date_heure_debut DESC");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Événements - Système de Réservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Asset/css/style.css">
    <style>
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-dialog {
            margin-top: 100px;
            pointer-events: all !important;
        }

        .ripple {
            pointer-events: none !important;
        }

        .modal-footer .btn {
            position: relative;
            z-index: 1060 !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="calendar.php">
            <i class="bi bi-calendar-check me-2"></i>Système de Réservation
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="calendar.php" class="nav-link">
                        <i class="bi bi-calendar-week me-1"></i>Calendrier
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profil.php" class="nav-link">
                        <i class="bi bi-person-circle me-1"></i>Mon Profil
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?logout=1" class="nav-link">
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row fade-in">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Mes Événements</h2>
                <a href="calendar.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Nouvel événement
                </a>
            </div>
            
            <?php if (empty($events)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Vous n'avez pas encore d'événements.
                    <a href="calendar.php" class="alert-link">Créez votre premier événement</a>.
                </div>
            <?php else: ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchEvents" class="form-control" placeholder="Rechercher un événement...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Titre</th>
                                        <th>Date et Heure</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="eventsTableBody">
                                    <?php foreach ($events as $evt): ?>
                                    <tr class="event-row <?php echo (isset($_GET['id']) && $_GET['id'] == $evt['id']) ? 'table-primary' : ''; ?>"
                                        data-event-id="<?= $evt['id'] ?>">
                                        <td class="event-title">
                                            <strong><?= htmlspecialchars($evt['titre']) ?></strong>
                                        </td>
                                        <td class="event-date">
                                            <i class="bi bi-clock me-1 text-muted"></i>
                                            <?= date('d/m/Y', strtotime($evt['date_heure_debut'])) ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($evt['date_heure_debut'])) ?> - 
                                                <?= date('H:i', strtotime($evt['date_heure_fin'])) ?>
                                            </small>
                                        </td>
                                        <td class="event-description">
                                            <?= htmlspecialchars($evt['description'] ?: 'Aucune description') ?>
                                        </td>
                                        <td>
                                            <a href="events.php?id=<?= $evt['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div id="eventCount" class="small">
                            <?= count($events) ?> événement<?= count($events) > 1 ? 's' : '' ?> au total
                        </div>
                    </div>
                </div>
                
                <div id="noSearchResults" class="alert alert-warning" style="display: none;">
                    <i class="bi bi-exclamation-triangle me-2"></i>Aucun événement ne correspond à votre recherche.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <?php if ($event): ?>
                <div class="card fade-in">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Modifier l'événement
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="Asset/php/events.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                            
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($event['titre']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
                            </div>
                            
                            <div class="alert alert-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <strong>Date :</strong>
                                    <span class="ms-2"><?= date('d/m/Y', strtotime($event['date_heure_debut'])) ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock me-2"></i>
                                    <strong>Heure :</strong>
                                    <span class="ms-2">
                                        <?= date('H:i', strtotime($event['date_heure_debut'])) ?> - 
                                        <?= date('H:i', strtotime($event['date_heure_fin'])) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-1"></i>Enregistrer
                                </button>
                                
                                <button type="button" class="btn btn-danger" id="deleteButton">
                                    <i class="bi bi-trash me-1"></i>Supprimer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmation de suppression
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer cet événement ?</p>
                                <p><strong><?= htmlspecialchars($event['titre']) ?></strong></p>
                                <p class="text-muted">
                                    <?= date('d/m/Y', strtotime($event['date_heure_debut'])) ?> de
                                    <?= date('H:i', strtotime($event['date_heure_debut'])) ?> à
                                    <?= date('H:i', strtotime($event['date_heure_fin'])) ?>
                                </p>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Cette action est irréversible.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <form action="Asset/php/events.php" method="POST" id="deleteForm">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card bg-light fade-in">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="bi bi-info-circle me-2 text-primary"></i>Gestion des événements
                        </h4>
                        <p class="card-text">Sélectionnez un événement dans la liste pour le modifier ou le supprimer.</p>
                        <p class="card-text">Vous pouvez créer de nouveaux événements depuis le calendrier.</p>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="calendar.php" class="btn btn-primary">
                                <i class="bi bi-calendar-plus me-2"></i>Créer un événement
                            </a>
                        </div>
                        
                        <?php if (!empty($events)): ?>
                            <hr>
                            <h5 class="mt-3">
                                <i class="bi bi-bar-chart me-2 text-primary"></i>Statistiques
                            </h5>
                            <?php
                                $upcomingCount = 0;
                                $now = new DateTime();
                                foreach ($events as $evt) {
                                    $eventDate = new DateTime($evt['date_heure_debut']);
                                    if ($eventDate > $now) {
                                        $upcomingCount++;
                                    }
                                }
                            ?>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Événements à venir
                                    <span class="badge bg-primary rounded-pill"><?= $upcomingCount ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Événements passés
                                    <span class="badge bg-secondary rounded-pill"><?= count($events) - $upcomingCount ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total
                                    <span class="badge bg-info rounded-pill"><?= count($events) ?></span>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="Asset/js/animations.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchEvents');
        const eventRows = document.querySelectorAll('.event-row');
        const noResultsMessage = document.getElementById('noSearchResults');
        const eventCount = document.getElementById('eventCount');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                eventRows.forEach(row => {
                    const title = row.querySelector('.event-title').textContent.toLowerCase();
                    const date = row.querySelector('.event-date').textContent.toLowerCase();
                    const description = row.querySelector('.event-description').textContent.toLowerCase();
                    
                    if (title.includes(searchTerm) || date.includes(searchTerm) || description.includes(searchTerm) || searchTerm === '') {
                        row.style.display = '';
                        visibleCount++;
                        
                        row.classList.add('search-match');
                        setTimeout(() => {
                            row.classList.remove('search-match');
                        }, 500);
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                if (eventCount) {
                    eventCount.textContent = `${visibleCount} événement${visibleCount > 1 ? 's' : ''} sur ${eventRows.length}`;
                }
                
                if (noResultsMessage) {
                    if (visibleCount === 0 && searchTerm !== '') {
                        noResultsMessage.style.display = 'block';
                    } else {
                        noResultsMessage.style.display = 'none';
                    }
                }
            });
        }
        
        eventRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.classList.add('row-hover');
            });
            
            row.addEventListener('mouseleave', function() {
                this.classList.remove('row-hover');
            });
            
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const eventId = this.dataset.eventId;
                window.location.href = `events.php?id=${eventId}`;
            });
        });
        
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                modal.show();
            });
        }

        const style = document.createElement('style');
        style.textContent = `
            .event-row {
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .row-hover {
                background-color: rgba(52, 152, 219, 0.05);
            }
            
            .search-match {
                animation: highlightRow 0.5s ease;
            }
            
            @keyframes highlightRow {
                0% { background-color: rgba(52, 152, 219, 0.2); }
                100% { background-color: transparent; }
            }
        `;
        document.head.appendChild(style);
    });
</script>

</body>
</html>
