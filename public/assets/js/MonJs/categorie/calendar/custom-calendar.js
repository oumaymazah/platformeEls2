
/* Updated JavaScript for the calendar */
$(document).ready(function() {
    // Variable globale pour suivre si nous devons mettre en surbrillance le jour sélectionné
    var dayToHighlight = null;
    
    // Variable pour stocker la date de début sélectionnée
    var selectedStartDate = null;
    
    // Variable pour suivre si un calendrier est actuellement ouvert
    var activeCalendar = null;
    
    // Fonction pour créer un calendrier personnalisé
    function initCustomDatepicker(selector, isEndDate = false) {
        var today = new Date();
        var currentMonth = today.getMonth();
        var currentYear = today.getFullYear();
        
        // Élément datepicker
        var $datepicker = $(selector);
        
        // Initialiser avec la date actuelle du champ s'il en a une
        var initialDateStr = $datepicker.val();
        if (initialDateStr) {
            var initialDate = parseDate(initialDateStr);
            if (initialDate) {
                currentMonth = initialDate.getMonth();
                currentYear = initialDate.getFullYear();
            }
        }
        
        // Générer un ID unique pour ce calendrier
        var calendarId = 'calendar-' + Math.random().toString(36).substr(2, 9);
        
        // Conteneur du calendrier
        var $calendar = $('<div id="' + calendarId + '" class="custom-calendar-container"></div>');
        var $calendarWrapper = $('<div class="custom-calendar-wrapper"></div>');
        
        // En-tête avec mois et navigation
        var $header = $('<div class="calendar-header"></div>');
        var $prevBtn = $('<button type="button" class="calendar-nav-btn prev-btn"><i class="fa fa-chevron-left"></i></button>');
        var $nextBtn = $('<button type="button" class="calendar-nav-btn next-btn"><i class="fa fa-chevron-right"></i></button>');
        var $monthYear = $('<div class="month-year">APRIL, 2025</div>');
        
        $header.append($prevBtn).append($monthYear).append($nextBtn);
        
        // Corps du calendrier avec jours de la semaine
        var $calendarBody = $('<div class="calendar-body"></div>');
        var $weekdays = $('<div class="weekdays"></div>');
        var weekdays = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
        
        weekdays.forEach(function(day) {
            $weekdays.append('<div class="weekday">' + day + '</div>');
        });
        
        // Grille des jours
        var $daysGrid = $('<div class="days-grid"></div>');
        
        $calendarBody.append($weekdays).append($daysGrid);
        $calendarWrapper.append($header).append($calendarBody);
        $calendar.append($calendarWrapper);
        
        // Ajouter le style CSS pour la mise en évidence bleue
        if (!document.getElementById('custom-calendar-styles')) {
            var styleElement = document.createElement('style');
            styleElement.id = 'custom-calendar-styles';
            styleElement.textContent = `
                .highlight-blue {
                    background-color: #4481dd !important;
                    color: white !important;
                }
                .highlight-blue:hover {
                    background-color: #4481dd !important;
                    color: white !important;
                }
                .day {
                    cursor: pointer;
                    transition: background-color 0.2s;
                }
                .day:hover:not(.highlight-blue):not(.disabled) {
                    background-color: #f0f0f0;
                }
                .disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }
                .custom-calendar-container {
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    width: 260px;
                    height: auto;
                    font-family: Arial, sans-serif;
                    position: absolute;
                    z-index: 1000;
                }
                .days-grid {
                    display: grid;
                    grid-template-columns: repeat(7, 1fr);
                    gap: 3px;
                    padding: 8px 12px 15px;
                }
                .day {
                    width: 28px;
                    height: 28px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 13px;
                    border-radius: 4px;
                    margin: 1px auto;
                }
            `;
            document.head.appendChild(styleElement);
        }
        
        function renderCalendar(month, year) {
            $monthYear.text(getMonthName(month) + ', ' + year);
            $daysGrid.empty();
            
            var firstDay = new Date(year, month, 1).getDay();
            var daysInMonth = new Date(year, month + 1, 0).getDate();
            var daysInPrevMonth = new Date(year, month, 0).getDate();
            
            // Obtenir la date actuellement sélectionnée (si elle existe)
            var selectedDateStr = $datepicker.val();
            var selectedDate = selectedDateStr ? parseDate(selectedDateStr) : null;
            
            // Jours du mois précédent
            for (var i = firstDay - 1; i >= 0; i--) {
                var dayNum = daysInPrevMonth - i;
                var prevMonth = month - 1;
                var prevYear = year;
                
                if (prevMonth < 0) {
                    prevMonth = 11;
                    prevYear--;
                }
                
                var currentDate = new Date(prevYear, prevMonth, dayNum);
                var isDisabled = isEndDate && selectedStartDate && currentDate < selectedStartDate;
                
                var $dayEl = $('<div class="day prev-month' + (isDisabled ? ' disabled' : '') + '">' + dayNum + '</div>');
                
                // Vérifier si c'est le jour à mettre en évidence
                if (dayToHighlight && dayToHighlight.day === dayNum && 
                    dayToHighlight.month === prevMonth && 
                    dayToHighlight.year === prevYear) {
                    $dayEl.addClass('highlight-blue');
                    dayToHighlight = null; // Réinitialiser après mise en évidence
                }
                
                // Permettre la navigation vers le mois précédent
                if (!isDisabled) {
                    $dayEl.on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var day = parseInt($(this).text());
                        
                        // Réinitialiser toutes les surbrillances
                        $daysGrid.find('.day').removeClass('highlight-blue');
                        
                        // Définir le jour à mettre en évidence dans le nouveau mois
                        dayToHighlight = {
                            day: day,
                            month: prevMonth,
                            year: prevYear
                        };
                        
                        goToPrevMonth();
                    });
                }
                
                $daysGrid.append($dayEl);
            }
            
            // Jours du mois actuel
            for (var day = 1; day <= daysInMonth; day++) {
                var date = new Date(year, month, day);
                var isToday = date.toDateString() === today.toDateString();
                var isDisabled = false;
                
                // Pour le calendrier de date de fin, désactiver les dates antérieures à la date de début
                if (isEndDate && selectedStartDate) {
                    isDisabled = date < selectedStartDate;
                } else {
                    // Pour le calendrier normal, on désactive les dates passées
                    isDisabled = date < today && !isToday;
                }
                
                // Vérifier si ce jour correspond à la date sélectionnée
                var isSelected = selectedDate && 
                                date.getDate() === selectedDate.getDate() && 
                                date.getMonth() === selectedDate.getMonth() && 
                                date.getFullYear() === selectedDate.getFullYear();
                
                // Vérifier si c'est le jour à mettre en évidence
                var shouldHighlight = dayToHighlight && dayToHighlight.day === day && 
                                    dayToHighlight.month === month && 
                                    dayToHighlight.year === year;
                
                var $dayEl = $('<div class="day' + 
                    (isToday ? ' today' : '') +
                    (isDisabled ? ' disabled' : '') +
                    (isSelected ? ' selected' : '') +
                    (shouldHighlight ? ' highlight-blue' : '') +
                    ' current-month">' + day + '</div>');
                
                if (!isDisabled) {
                    $dayEl.on('click', function() {
                        // Retirer la classe highlight-blue de tous les jours
                        $daysGrid.find('.day').removeClass('highlight-blue');
                        
                        // Retirer la classe selected de tous les jours
                        $daysGrid.find('.day').removeClass('selected');
                        
                        // Ajouter la classe selected et highlight-blue au jour cliqué
                        $(this).addClass('selected highlight-blue');
                        
                        // Réinitialiser dayToHighlight
                        dayToHighlight = null;
                        
                        var selectedDay = parseInt($(this).text());
                        var selectedDate = new Date(year, month, selectedDay);
                        $datepicker.val(formatDate(selectedDate));
                        $calendar.hide();
                        
                        // Si c'est le datepicker de début, mettre à jour la date de début
                        if (!isEndDate) {
                            selectedStartDate = selectedDate;
                            
                            // Si la date de fin est déjà sélectionnée et qu'elle est antérieure à la nouvelle date de début
                            var endDateStr = $('#end_date').val();
                            if (endDateStr) {
                                var endDate = parseDate(endDateStr);
                                if (endDate && endDate < selectedStartDate) {
                                    // Réinitialiser la date de fin
                                    $('#end_date').val('');
                                }
                            }
                        }
                        
                        // Réinitialiser activeCalendar
                        activeCalendar = null;
                        
                        // Trigger change event pour mettre à jour les validations
                        $datepicker.trigger('change');
                    });
                }
                
                $daysGrid.append($dayEl);
                
                // Si c'est le jour à mettre en évidence, réinitialiser après la mise en évidence
                if (shouldHighlight) {
                    dayToHighlight = null;
                }
            }
            
            // Jours du mois suivant
            var totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
            var remainingCells = totalCells - (firstDay + daysInMonth);
            
            for (var j = 1; j <= remainingCells; j++) {
                var nextMonth = month + 1;
                var nextYear = year;
                
                if (nextMonth > 11) {
                    nextMonth = 0;
                    nextYear++;
                }
                
                var $dayEl = $('<div class="day next-month">' + j + '</div>');
                
                // Vérifier si c'est le jour à mettre en évidence
                if (dayToHighlight && dayToHighlight.day === j && 
                    dayToHighlight.month === nextMonth && 
                    dayToHighlight.year === nextYear) {
                    $dayEl.addClass('highlight-blue');
                    dayToHighlight = null; // Réinitialiser après mise en évidence
                }
                
                // Permettre la navigation vers le mois suivant
                $dayEl.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var day = parseInt($(this).text());
                    
                    // Réinitialiser toutes les surbrillances
                    $daysGrid.find('.day').removeClass('highlight-blue');
                    
                    // Définir le jour à mettre en évidence dans le nouveau mois
                    dayToHighlight = {
                        day: day,
                        month: nextMonth,
                        year: nextYear
                    };
                    
                    goToNextMonth();
                });
                
                $daysGrid.append($dayEl);
            }
        }
        
        // Fonctions de navigation entre les mois
        function goToPrevMonth() {
            var newMonth = currentMonth - 1;
            var newYear = currentYear;
            
            if (newMonth < 0) {
                newMonth = 11;
                newYear--;
            }
            
            currentMonth = newMonth;
            currentYear = newYear;
            
            renderCalendar(currentMonth, currentYear);
        }
        
        function goToNextMonth() {
            var newMonth = currentMonth + 1;
            var newYear = currentYear;
            
            if (newMonth > 11) {
                newMonth = 0;
                newYear++;
            }
            
            currentMonth = newMonth;
            currentYear = newYear;
            
            renderCalendar(currentMonth, currentYear);
        }
        
        // Navigation
        $prevBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Réinitialiser dayToHighlight lors de la navigation manuelle
            dayToHighlight = null;
            
            goToPrevMonth();
        });
        
        $nextBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Réinitialiser dayToHighlight lors de la navigation manuelle
            dayToHighlight = null;
            
            goToNextMonth();
        });
        
        // Afficher/masquer le calendrier
        $datepicker.on('click focus', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Si ce calendrier est déjà ouvert, ne rien faire
            if (activeCalendar === calendarId && $calendar.is(':visible')) {
                return;
            }
            
            // Fermer tous les autres calendriers ouverts
            $('.custom-calendar-container').hide();
            
            var offset = $datepicker.offset();
            var height = $datepicker.outerHeight();
            
            $calendar.css({
                top: offset.top + height + 5,
                left: offset.left
            }).show();
            
            // Mettre à jour activeCalendar
            activeCalendar = calendarId;
            
            // Si une date est déjà sélectionnée, afficher ce mois-là
            var dateStr = $datepicker.val();
            if (dateStr) {
                var date = parseDate(dateStr);
                if (date) {
                    currentMonth = date.getMonth();
                    currentYear = date.getFullYear();
                }
            }
            
            // Réinitialiser dayToHighlight car nous affichons un nouveau calendrier
            dayToHighlight = null;
            
            renderCalendar(currentMonth, currentYear);
        });
        
        // Fermer le calendrier en cliquant ailleurs
        $(document).on('click', function(e) {
            if (!$calendar.is(e.target) && $calendar.has(e.target).length === 0 && 
                !$datepicker.is(e.target) && !$(e.target).hasClass('calendar-nav-btn') && 
                !$(e.target).hasClass('fa-chevron-left') && !$(e.target).hasClass('fa-chevron-right')) {
                $calendar.hide();
                if (activeCalendar === calendarId) {
                    activeCalendar = null;
                }
            }
        });
        
        // Ajouter le calendrier au DOM
        $('body').append($calendar);
        $calendar.hide();
        
        // Retourner l'ID du calendrier pour référence
        return calendarId;
    }
    
    // Fonctions utilitaires
    function getMonthName(month) {
        var months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
        return months[month];
    }
    
    function formatDate(date) {
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        
        return (day < 10 ? '0' + day : day) + '/' + (month < 10 ? '0' + month : month) + '/' + year;
    }
    
    function parseDate(dateStr) {
        if (!dateStr) return null;
        
        var parts = dateStr.split('/');
        if (parts.length !== 3) return null;
        
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }
    
    // Mettre à jour la date de début lorsque sa valeur change
    $('#start_date').on('change', function() {
        var startDateStr = $(this).val();
        if (startDateStr) {
            selectedStartDate = parseDate(startDateStr);
            
            // Si la date de fin est déjà sélectionnée et qu'elle est antérieure à la nouvelle date de début
            var endDateStr = $('#end_date').val();
            if (endDateStr) {
                var endDate = parseDate(endDateStr);
                if (endDate && endDate < selectedStartDate) {
                    // Réinitialiser la date de fin
                    $('#end_date').val('');
                }
            }
        } else {
            selectedStartDate = null;
        }
    });
    
    // Initialiser les datepickers personnalisés
    initCustomDatepicker('#start_date', false);
    initCustomDatepicker('#end_date', true);
});