//Initialize DOM elements for search functionality
const searchInput = document.getElementById('searchInput');
const cards = document.querySelectorAll('.dashboard-card');
const noResults = document.getElementById('noResults');

//Filter cards based on search input and show/hide "No results" message
searchInput.addEventListener('keyup', () => {
    const filter = searchInput.value.toLowerCase();
    let visibleCards = 0;
    cards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const text = card.querySelector('p').textContent.toLowerCase();
        if (title.includes(filter) || text.includes(filter)) {
            card.style.display = 'block';
            visibleCards++;
        } else {
            card.style.display = 'none';
        }
    });
    //Only show "No results" if search has text but no matches
    if (visibleCards === 0 && filter !== '') {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
});
