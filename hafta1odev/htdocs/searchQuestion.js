document.getElementById('searchForm').addEventListener('submit', function(event) {
    let resultsDiv = document.getElementById('searchResults');
    let message = document.createElement('h1');
    message.textContent = 'Bu kodu yazmayı bilmediğim için bu sayfa böyle görünüyor';
    resultsDiv.appendChild(message);
});