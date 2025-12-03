// Confirmation dialogs
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('[required]');
    
    for(let input of inputs) {
        if(!input.value.trim()) {
            alert('Please fill in all required fields');
            input.focus();
            return false;
        }
    }
    return true;
}

// Password match validation
function validatePasswordMatch(password, confirmPassword) {
    if(password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }
    return true;
}

// Date validation
function validateDate(dateString) {
    const selectedDate = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if(selectedDate < today) {
        alert('Please select a future date');
        return false;
    }
    return true;
}

// Auto-hide alerts
window.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
});

// Table search functionality
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    for(let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        
        for(let j = 0; j < td.length; j++) {
            if(td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if(txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}
