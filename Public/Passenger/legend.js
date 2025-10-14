//Lindo

const timeElements = document.querySelectorAll('.time');
const statuses = ['on-time', 'delayed', 'not-coming'];

// Convert NodeList to array
let timeArray = Array.from(timeElements);

// Function to shuffle time elements randomly
function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
  }

function cycleTimes() {
    // Shuffle time elements on each full cycle
    timeArray = shuffle(timeArray);

    timeArray.forEach((el, i) => {
        setTimeout(() => {
        // Clear previous statuses
        timeElements.forEach(elem => {
            elem.parentElement.removeAttribute('data-status');
        });

        // Randomly pick a status
        const status = statuses[Math.floor(Math.random() * statuses.length)];

        // Apply status
        el.parentElement.setAttribute('data-status', status);
    }, i * 5000); // Wait 5 seconds between each time
    });

    // Repeat after full cycle
    setTimeout(cycleTimes, timeArray.length * 5000);
  }

cycleTimes(); // Start the cycle