console.log('JS Loaded');
document.addEventListener('DOMContentLoaded', () => {
    const showPasswordIcons = document.querySelectorAll('.show-password');
    
    showPasswordIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const inputBox = this.closest('.input-box');
            const input = inputBox.querySelector('input[type="password"], input[type="text"]');
            
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('bx-hide');
                    this.classList.add('bx-show');
                } else {
                    input.type = 'password';
                    this.classList.remove('bx-show');
                    this.classList.add('bx-hide');
                }
            }
        });
    });
    const listings = document.querySelectorAll('.service-category-listing');
    let initialItemsToShow = 3;
    if (window.innerWidth < 576) {
        initialItemsToShow = 2;
    } else if (window.innerWidth < 768) { 
        initialItemsToShow = 2;
    } else if (window.innerWidth < 992) { 
        initialItemsToShow = 2;
    }
    listings.forEach(listing => {
        const grid = listing.querySelector('.space-summary-grid');
        if (!grid) return;

        const items = Array.from(grid.children);
        const showAllBtn = listing.querySelector('.show-all-btn');
        const headerElement = listing.querySelector('.category-listing-header h2');
        let serviceTypeName = "Spaces";
        if (headerElement) {
            serviceTypeName = headerElement.textContent
                .replace("Spaces for ", "")
                .replace("Venues for ", "")
                .replace("Exclusive ", "")
                .replace(" Rooms", "");
        }
        items.forEach((item, index) => {
            if (index >= initialItemsToShow) {
                item.style.display = 'none';
            } else {
                item.style.display = ''; 
            }
        });
        if (showAllBtn) {
            showAllBtn.innerHTML = `Show All ${serviceTypeName} <i class='bx bx-chevron-down'></i>`;
            if (items.length <= initialItemsToShow) {
                showAllBtn.style.display = 'none';
            } else {
                showAllBtn.style.display = 'inline-block'; 
            }
            showAllBtn.addEventListener('click', function () {
                const isShowingAll = grid.classList.contains('showing-all');
                items.forEach((item, index) => {
                    if (isShowingAll) {
                        if (index >= initialItemsToShow) {
                            item.classList.add('fade-out-item');
                            setTimeout(() => {
                                item.style.display = 'none';
                                item.classList.remove('fade-out-item');
                            }, 300);
                        }
                    } else {
                        if (index >= initialItemsToShow) {
                            item.style.display = ''; 
                            item.classList.add('fade-in-item');
                            setTimeout(() => item.classList.remove('fade-in-item'), 310);
                        }
                    }
                });
                grid.classList.toggle('showing-all');
                if (grid.classList.contains('showing-all')) {
                    this.innerHTML = `Show Fewer ${serviceTypeName} <i class='bx bx-chevron-up'></i>`;
                } else {
                    this.innerHTML = `Show All ${serviceTypeName} <i class='bx bx-chevron-down'></i>`;
                }
            });
        } else if (items.length > 0) { 
            items.forEach(item => item.style.display = '');
        }
    });
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
          'resizeDuration': 200,
          'wrapAround': true
        });
    } else {
        console.warn('Lightbox2 library not found. Image gallery will not have lightbox functionality.');
    }
    const availabilityTrigger = document.querySelector('.availability-trigger');
    const availabilityPopup = document.getElementById('availabilityPopup');
    const closePopupBtn = document.querySelector('.close-popup-btn');
    const availabilityFormPopup = document.querySelector('.availability-form-popup'); 

    if (availabilityTrigger && availabilityPopup) {
        availabilityTrigger.addEventListener('click', () => {
            availabilityPopup.classList.add('active');
        });
    }
    if (closePopupBtn && availabilityPopup) {
        closePopupBtn.addEventListener('click', () => {
            availabilityPopup.classList.remove('active');
        });
    }
    if (availabilityPopup) {
        availabilityPopup.addEventListener('click', (e) => {
            if (e.target === availabilityPopup) { 
                availabilityPopup.classList.remove('active');
            }
        });
    }
    if (availabilityFormPopup) { 
        const checkButton = availabilityFormPopup.querySelector('button'); 
        const statusElementPopup = availabilityFormPopup.querySelector('.availability-status-popup');

        if (checkButton && statusElementPopup) {
            checkButton.addEventListener('click', function() {
                const dateInput = availabilityFormPopup.querySelector('input[type="date"]');
                const sessionSelect = availabilityFormPopup.querySelector('select');

                if (!dateInput.value) {
                    statusElementPopup.textContent = 'Please select a date.';
                    statusElementPopup.style.color = 'orange';
                    return;
                }
                statusElementPopup.textContent = 'Checking...';
                statusElementPopup.style.color = '#777';
                setTimeout(() => {
                    const isAvailable = Math.random() < 0.6;
                    if (isAvailable) {
                        statusElementPopup.textContent = `Available on ${dateInput.value} for the selected session!`;
                        statusElementPopup.style.color = 'green';
                    } else {
                        statusElementPopup.textContent = `Sorry, booked on ${dateInput.value} for the selected session. Please try another time.`;
                        statusElementPopup.style.color = 'red';
                    }
                }, 1500);
            });
        }
    }
});