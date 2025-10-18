<footer class="admin-footer animate__animated animate__fadeInUp text-center">
    <p>&copy; <?php echo date('Y'); ?> 
        <strong>Furniture Store Admin Panel</strong>. 
        All rights reserved.
    </p>
</footer>

<style>
    .admin-footer {
        background: linear-gradient(90deg, #2c3e50, #34495e);
        color: #ecf0f1;
        padding: 14px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0.5px;
        margin-top: 30px;
        box-shadow: 0 -3px 10px rgba(0,0,0,0.2);
        position: relative;
        z-index: 10;
        transition: all 0.4s ease-in-out;
    }
    .admin-footer:hover {
        background: linear-gradient(90deg, #34495e, #2c3e50);
        color: #f1c40f; /* gold hover effect */
        transform: translateY(-3px);
    }
    .admin-footer p {
        margin: 0;
        font-weight: 400;
        animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
