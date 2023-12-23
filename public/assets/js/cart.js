// Définissons d'abord la fonction updateQuantity
function updateQuantity( cartItemId, quantity ) {
    fetch(`/cart/update/${ cartItemId }`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({quantity: quantity}),
    }).then(response => response.json())
        .then(data =>
        {
            if (data.status === 'success') {
                let itemPrice = parseFloat(document.querySelector('#item-price-' + cartItemId).textContent);
                document.querySelector('#item-total-' + cartItemId).textContent = (
                    itemPrice * quantity).toFixed(2);

                // Recalcul du total
                let totalElement = document.querySelector('#total');
                let total = Array.from(document.querySelectorAll('.item-total'))
                    .reduce(( total, itemTotalElement ) =>
                    {
                        return total + parseFloat(itemTotalElement.textContent);
                    }, 0);

                totalElement.textContent = total.toFixed(2) + '€';  // Assurez-vous que le format convient

                console.log(data.message);
            } else if (data.status === 'error') {
                console.error(data.message);
            }
        }).catch(error => console.error('Une erreur s\'est produite lors de la mise à jour de la quantité :', error));
}

window.addEventListener('DOMContentLoaded', () =>
{
    let decreaseButtons = document.querySelectorAll('.decrease-quantity');
    let increaseButtons = document.querySelectorAll('.increase-quantity');

    decreaseButtons.forEach(button =>
    {
        button.addEventListener('click', function ()
        {
            let targetId = this.dataset.cartitemId;
            let quantityDisplay = this.parentElement.querySelector('.quantity-display');
            let newQuantity = parseInt(quantityDisplay.textContent) - 1;
            newQuantity = newQuantity < 1 ? 1 : newQuantity; // Make sure quantity is at least 1
            quantityDisplay.textContent = newQuantity;
            updateQuantity(targetId, newQuantity);
        });
    });

    increaseButtons.forEach(button =>
    {
        button.addEventListener('click', function ()
        {
            let targetId = this.dataset.cartitemId;
            let quantityDisplay = this.parentElement.querySelector('.quantity-display');
            let newQuantity = parseInt(quantityDisplay.textContent) + 1;
            quantityDisplay.textContent = newQuantity;
            updateQuantity(targetId, newQuantity);
        });
    });
});