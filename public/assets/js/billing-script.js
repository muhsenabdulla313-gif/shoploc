// Toggle sidebar
document.getElementById('toggleSidebar').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    sidebar.classList.toggle('show');
    mainContent.classList.toggle('expanded');
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('toggleSidebar');
    
    if (window.innerWidth < 992) {
        if (!sidebar.contains(event.target) && event.target !== toggleButton && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            document.getElementById('mainContent').classList.remove('expanded');
        }
    }
});

function viewOrderDetails(id) {
    // Fetch order details
    fetch('/billing/orders/' + id)
        .then(res => res.json())
        .then(data => {
            // Populate Modal
            document.getElementById('modalOrderId').innerText = data.id;
            document.getElementById('modalOrderIdDetail').innerText = data.id;
            document.getElementById('modalCustomerName').innerText = data.first_name + ' ' + data.last_name;
            document.getElementById('modalCustomerEmail').innerText = data.email;
            document.getElementById('modalCustomerPhone').innerText = data.phone;
            
            document.getElementById('modalOrderDate').innerText = new Date(data.created_at).toLocaleDateString();
            document.getElementById('modalOrderStatus').innerText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            document.getElementById('modalPaymentMethod').innerText = data.payment_method.toUpperCase();
            
            document.getElementById('modalAddress').innerHTML = data.address + '<br>' + 
                data.city + ', ' + data.state + ' ' + data.zip;
            
            // Items
            let itemsHtml = '';
            data.items.forEach(item => {
                // Format color and size display
                const colorDisplay = item.color ? '<br><small>Color: ' + item.color + '</small>' : '';
                const sizeDisplay = item.size ? '<br><small>Size: ' + item.size + '</small>' : '';
                
                itemsHtml += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="` + (item.product_image || 'https://placehold.co/40') + `" style="width:40px; height:40px; object-fit:cover; margin-right:10px;">
                                ` + item.product_name + `
                                ` + colorDisplay + `
                                ` + sizeDisplay + `
                            </div>
                        </td>
                        <td>₹` + parseFloat(item.price).toFixed(2) + `</td>
                        <td>` + item.quantity + `</td>
                        <td>₹` + parseFloat(item.total).toFixed(2) + `</td>
                    </tr>
                `;
            });
            
            document.getElementById('modalItemsBody').innerHTML = itemsHtml;
            
            // Show Modal
            const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            modal.show();
        })
        .catch(err => console.error(err));
}