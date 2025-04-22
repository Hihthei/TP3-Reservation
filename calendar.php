<?php
session_start();
require_once 'Asset/php/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ?");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();

$eventsMap = [];
foreach ($events as $event) {
    $dateTime = new DateTime($event['date_heure_debut']);
    $day = $dateTime->format('N');
    $hour = $dateTime->format('G');
    $eventsMap[$day][$hour] = $event;
}

$now = new DateTime();
$currentDay = $now->format('N');
$currentDate = $now->format('j');
$currentMonth = $now->format('n');
$currentYear = $now->format('Y');

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Réservation - Calendrier</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Asset/css/style.css">
</head>
<body>
    <div class="container-fluid">
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
                            <a href="profil.php" class="nav-link">
                                <i class="bi bi-person-circle me-1"></i>Mon Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="events.php" class="nav-link">
                                <i class="bi bi-list-check me-1"></i>Mes Événements
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

        <div class="calendar-container fade-in">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-center mb-0">Mon Calendrier</h2>
                <div class="calendar-controls">
                    <button id="prevWeek" class="btn btn-outline-primary btn-sm me-2">
                        <i class="bi bi-chevron-left"></i> Semaine précédente
                    </button>
                    <button id="today" class="btn btn-outline-primary btn-sm me-2">
                        Aujourd'hui
                    </button>
                    <button id="nextWeek" class="btn btn-outline-primary btn-sm">
                        Semaine suivante <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            
            <div class="current-week-display text-center mb-3">
                <h5 id="currentWeekDisplay">Semaine du <span id="weekStart">...</span> au <span id="weekEnd">...</span></h5>
            </div>
            
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th width="70">Heure</th>
                        <th>Lundi</th>
                        <th>Mardi</th>
                        <th>Mercredi</th>
                        <th>Jeudi</th>
                        <th>Vendredi</th>
                        <th>Samedi</th>
                        <th>Dimanche</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($hour = 8; $hour <= 18; $hour++): ?>
                    <tr>
                        <td class="hour-cell"><?php echo $hour . ':00'; ?></td>
                        <?php for ($day = 1; $day <= 7; $day++): ?>
                        <td class="calendar-cell" data-day="<?php echo $day; ?>" data-hour="<?php echo $hour; ?>">
                            <?php if (isset($eventsMap[$day][$hour])): ?>
                                <div class="event" 
                                     data-event-id="<?php echo $eventsMap[$day][$hour]['id']; ?>"
                                     title="<?php echo htmlspecialchars($eventsMap[$day][$hour]['description'] ?: 'Aucune description'); ?>">
                                    <?php echo htmlspecialchars($eventsMap[$day][$hour]['titre']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            
            <div class="calendar-legend mt-3">
                <div class="d-flex align-items-center">
                    <span class="me-3"><i class="bi bi-info-circle text-primary"></i> Cliquez sur une case pour ajouter un événement</span>
                    <span><i class="bi bi-info-circle text-primary"></i> Cliquez sur un événement pour le modifier</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Ajouter un événement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm" action="Asset/php/events.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" id="action" name="action" value="create">
                        <input type="hidden" id="event_id" name="event_id" value="">
                        <input type="hidden" id="day" name="day" value="">
                        <input type="hidden" id="hour" name="hour" value="">
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3" id="datetime-display">
                            <div class="alert alert-info">
                                <i class="bi bi-calendar-event me-2"></i>
                                <span id="date-time-text"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Ajouter</button>
                            <button type="button" class="btn btn-danger" id="deleteBtn" style="display:none;">Supprimer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="Asset/js/animations.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const currentDay = now.getDay() || 7;
            const currentDate = now.getDate();
            const currentMonth = now.getMonth();
            const currentYear = now.getFullYear();
            
            const mondayOffset = 1 - currentDay;
            let mondayDate = new Date(currentYear, currentMonth, currentDate + mondayOffset);
            
            function updateWeekDisplay() {
                const sundayDate = new Date(mondayDate);
                sundayDate.setDate(mondayDate.getDate() + 6);
                
                const weekStartElem = document.getElementById('weekStart');
                const weekEndElem = document.getElementById('weekEnd');
                
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                weekStartElem.textContent = mondayDate.toLocaleDateString('fr-FR', options);
                weekEndElem.textContent = sundayDate.toLocaleDateString('fr-FR', options);
                
                highlightToday();
            }
            
            function highlightToday() {
                document.querySelectorAll('td.today').forEach(cell => {
                    cell.classList.remove('today');
                });
                
                const today = new Date();
                const mondayOfToday = new Date(today);
                mondayOfToday.setDate(today.getDate() - (today.getDay() || 7) + 1);
                
                if (mondayDate.toDateString() === mondayOfToday.toDateString()) {
                    const todayDayOfWeek = today.getDay() || 7;
                    
                    document.querySelectorAll(`td:nth-child(${todayDayOfWeek + 1})`).forEach(cell => {
                        cell.classList.add('today');
                    });
                }
            }
            
            document.getElementById('prevWeek').addEventListener('click', function() {
                mondayDate.setDate(mondayDate.getDate() - 7);
                updateWeekDisplay();
            });
            
            document.getElementById('nextWeek').addEventListener('click', function() {
                mondayDate.setDate(mondayDate.getDate() + 7);
                updateWeekDisplay();
            });
            
            document.getElementById('today').addEventListener('click', function() {
                mondayDate = new Date(currentYear, currentMonth, currentDate + mondayOffset);
                updateWeekDisplay();
            });
            
            updateWeekDisplay();
            
            document.querySelectorAll('.calendar-cell').forEach(cell => {
                cell.addEventListener('click', function(e) {
                    if (e.target.classList.contains('event')) return;
                    
                    const day = this.dataset.day;
                    const hour = this.dataset.hour;
                    
                    const eventDate = new Date(mondayDate);
                    eventDate.setDate(mondayDate.getDate() + parseInt(day) - 1);
                    eventDate.setHours(hour, 0, 0, 0);
                    
                    const dateTimeStr = eventDate.toLocaleString('fr-FR', {
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    const formattedDateTime = `${eventDate.getFullYear()}-${(eventDate.getMonth()+1).toString().padStart(2, '0')}-${eventDate.getDate().toString().padStart(2, '0')} ${eventDate.getHours().toString().padStart(2, '0')}:00:00`;
                    
                    document.getElementById('eventModalLabel').textContent = 'Ajouter un événement';
                    document.getElementById('action').value = 'create';
                    document.getElementById('event_id').value = '';
                    document.getElementById('titre').value = '';
                    document.getElementById('description').value = '';
                    document.getElementById('day').value = day;
                    document.getElementById('hour').value = hour;
                    document.getElementById('date-time-text').textContent = dateTimeStr;
                    document.getElementById('submitBtn').textContent = 'Ajouter';
                    document.getElementById('deleteBtn').style.display = 'none';
                    
                    let dateTimeInput = document.getElementById('date_heure_debut');
                    if (!dateTimeInput) {
                        dateTimeInput = document.createElement('input');
                        dateTimeInput.type = 'hidden';
                        dateTimeInput.id = 'date_heure_debut';
                        dateTimeInput.name = 'date_heure_debut';
                        document.getElementById('eventForm').appendChild(dateTimeInput);
                    }
                    dateTimeInput.value = formattedDateTime;
                    
                    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                });
            });
            
            document.querySelectorAll('.event').forEach(eventElem => {
                eventElem.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const eventId = this.dataset.eventId;
                    
                    window.location.href = `events.php?id=${eventId}`;
                });
            });
            
            document.getElementById('deleteBtn').addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
                    const eventId = document.getElementById('event_id').value;
                    document.getElementById('action').value = 'delete';
                    document.getElementById('eventForm').submit();
                }
            });
        });
    </script>
</body>
</html>