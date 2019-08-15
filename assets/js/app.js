/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

require('../scss/global.scss');

const bsn = require('bootstrap.native/dist/bootstrap-native-v4');

(function () {
    const endFightModal = document.getElementById('endFightModal');

    if (!endFightModal) {
        return;
    }

    const endFightModalInstance = new bsn.Modal(endFightModal);

    const endFightForm = endFightModal.querySelector('#endFightForm');

    const endActionForms = document.querySelectorAll('.end-action-form');

    for (const form of endActionForms) {
        form.addEventListener('submit', function (evt) {
            evt.preventDefault();

            endFightForm.action = form.action;

            endFightForm.querySelector('.csrf-token').value = form.querySelector('.csrf-token').value;

            endFightForm.querySelector('#winnerParticipantLabel').textContent = form.dataset.participant;
            endFightForm.querySelector('#winnerOpponentLabel').textContent = form.dataset.opponent;

            endFightModalInstance.show();
        });
    }
})();
