// Define a constant speed (e.g., in pixels per second)
const pixelsPerSecond = 100; 

// Select the marquee element(s)
const marquees = document.querySelectorAll('.marquee__content');

marquees.forEach(content => {
    const contentWidth = content.offsetWidth;
    console.log(contentWidth);
    // The distance is typically the width of the content plus the width of the container
    // for a seamless loop effect. A simpler approach can be just the content width.
    const distance = contentWidth; 

    // Calculate the duration in seconds
    const durationInSeconds = distance / pixelsPerSecond; 

    // Set the animation duration dynamically
    content.style.animationDuration = `${durationInSeconds}s`; 
});