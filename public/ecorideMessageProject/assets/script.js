document.querySelectorAll('.reply-btn').forEach(button => {
    button.addEventListener('click', () => {
        const replyBox = document.getElementById('reply-box');
        replyBox.style.display = 'block';

        const form = replyBox.querySelector('form');
        const senderInput = form.querySelector('input[name="sender_email"]');

        senderInput.value = button.dataset.sender;
        replyBox.scrollIntoView({ behavior: 'smooth' });
    });
});
