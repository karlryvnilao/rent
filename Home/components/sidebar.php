<!-- sidebar.php -->
<div class="sidebar">
    <div class="owner-profile">
        <a href="pg_landlord.php">
            <div class="profile">
                <?php if (!empty($landlordInfo['landlord_image'])) : ?>
                    <img id="profile_image" src="<?php echo $landlordInfo['landlord_image']; ?>" alt="profile">
                <?php else : ?>
                    <img id="profile_image" src="../images/noimage.jfif" alt="profile">
                <?php endif; ?>
            </div>
        </a>
        <h3><?php echo $username; ?></h3>
        <p>username</p> 
    </div>

    <button class="sidebar-btn" onclick="location.href='pg_bh.php'"><i class="fas fa-home"></i> Boarding House</button>
    <button class="sidebar-btn" onclick="location.href='pg_mytenant.php'"><i class="fas fa-users"></i> Tenants</button>
    <div class="space"></div>
    <a href="?logout=1">Logout</a>
</div>
