<style>
    .error-container {
        text-align: center;
        max-width: 600px;
        margin: auto;
    }

    .error-title {
        font-size: 6rem;
        font-weight: bold;
        color: #ff6b6b;
    }

    .error-text {
        font-size: 1.25rem;
        margin-top: 1rem;
        color: #6c757d;
    }
    .error-return {
        margin-bottom: 5rem;
    }
</style>
<div class="container">
<div class="error-container">
    <div class="error-title"><?=$code ?></div>
    <p class="error-text"><?=$message?></p>
    <p class="error-text">URL: <span class="text-danger" id="error-url"></span></p>
    <a href="<?= URL_BASE.'home/index' ;?>" class="btn btn-primary mt-4 error-return">Volver a la página principal</a>
</div>
</div>

<script>
    // Display the current URL in the error message
    document.getElementById('error-url').textContent = window.location.href;
</script>