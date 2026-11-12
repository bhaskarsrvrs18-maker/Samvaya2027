$(document).ready(function () {

    // Show or hide team registration fields
    function updateParticipation() {

        const type = $('input[name="type"]:checked').val();

        if (type === 'Team') {

            $('#teamFields').stop(true, true).slideDown(250);

            $('#team_name, #team_size, #leader_name, #leader_phone')
                .prop('required', true);

        } else {

            $('#teamFields').stop(true, true).slideUp(250);

            $('#team_name, #team_size, #leader_name, #leader_phone')
                .prop('required', false);
        }
    }

    $('input[name="type"]').on('change', updateParticipation);

    updateParticipation();

    // Form validation
    $('#festForm').on('submit', function (event) {

        const selectedEvents = $('input[name="events[]"]:checked').length;

        if (selectedEvents === 0) {
            event.preventDefault();
            alert('Please select at least one event.');
            $('#events')[0].scrollIntoView({ behavior: 'smooth' });
            return false;
        }

        const selectedDate = $('input[name="fest_date"]:checked').length;

        if (selectedDate === 0) {
            event.preventDefault();
            alert('Please select your preferred fest date.');
            return false;
        }

        const type = $('input[name="type"]:checked').val();

        if (type === 'Team') {

            const size = parseInt($('#team_size').val(), 10);

            if (!size || size < 2 || size > 30) {
                event.preventDefault();
                alert('Team size must be between 2 and 30.');
                return false;
            }
        }

        if (!this.checkValidity()) {
            event.preventDefault();
            this.reportValidity();
            return false;
        }

    });

});
