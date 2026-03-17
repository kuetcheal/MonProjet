<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['deconnect_account'])) {
    session_unset();
    session_destroy();
    header('Location: connexion.php');
    exit;
}

$isLoggedIn = isset($_SESSION['Id_compte']);

$userFullName = '';
if ($isLoggedIn) {
    $prenom = $_SESSION['user_firstname'] ?? '';
    $nom = $_SESSION['user_name'] ?? '';
    $userFullName = trim($prenom . ' ' . $nom);

    if ($userFullName === '') {
        $userFullName = 'Utilisateur';
    }
}

$userEmail = $isLoggedIn ? ($_SESSION['user_mail'] ?? '') : '';

$offerLabel = $isLoggedIn ? 'Mes offres' : 'Inscription';
$offerLink = $isLoggedIn ? 'offres.php' : 'inscription.php';

$easyLabel = 'EasyTravel';
$easyLink = $isLoggedIn ? 'Accueil.php' : 'connexion.php';
?>

<link rel="stylesheet" href="/MonProjet/includes/header.css">

<header class="gv-header">
    <div class="gv-header__inner">
        <a href="Accueil.php" class="gv-header__brand">
           <img src="/MonProjet/pictures/logo-general.jpg" alt="Logo Général Voyage" class="gv-header__logo">
        </a>

        <nav class="gv-header__desktop-nav">
            <a href="Accueil.php" class="gv-header__link gv-header__link--active">Accueil</a>
            <a href="reservations.php" class="gv-header__link">Réservations</a>
            <a href="services.php" class="gv-header__link">Services</a>
            <a href="contact.php" class="gv-header__link">Nos contacts</a>
            <a href="<?= htmlspecialchars($offerLink) ?>" class="gv-header__link">
                <?= htmlspecialchars($offerLabel) ?>
            </a>
            <a href="<?= htmlspecialchars($easyLink) ?>" class="gv-header__link">
                <?= htmlspecialchars($easyLabel) ?>
            </a>
        </nav>

        <div class="gv-header__actions">
            <button type="button" class="gv-header__lang">FR</button>

            <?php if ($isLoggedIn): ?>
                <button type="button" class="gv-header__profile-btn" onclick="gvOpenModal('gvAccountModal')">
                    <img id="profil" src="pictures/OIP.jpg" alt="Profil" class="gv-header__profile-img">
                </button>
            <?php endif; ?>

            <button id="gvBurgerButton" type="button" class="gv-header__burger" aria-label="Ouvrir le menu">
                <i class="fa fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<div id="gvMobileBackdrop" class="gv-mobile-backdrop"></div>

<aside id="gvMobileDrawer" class="gv-mobile-drawer" aria-hidden="true">
    <div class="gv-mobile-drawer__header">
        <img src="pictures/logo-general.jpg" alt="Logo Général Voyage" class="gv-mobile-drawer__logo">

        <button id="gvCloseDrawer" type="button" class="gv-mobile-drawer__close" aria-label="Fermer le menu">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <nav class="gv-mobile-drawer__nav">
        <a href="Accueil.php" class="gv-mobile-drawer__link gv-mobile-drawer__link--active">Accueil</a>
        <a href="reservations.php" class="gv-mobile-drawer__link">Réservations</a>
        <a href="services.php" class="gv-mobile-drawer__link">Services</a>
        <a href="contact.php" class="gv-mobile-drawer__link">Nos contacts</a>
        <a href="<?= htmlspecialchars($offerLink) ?>" class="gv-mobile-drawer__link">
            <?= htmlspecialchars($offerLabel) ?>
        </a>
        <a href="<?= htmlspecialchars($easyLink) ?>" class="gv-mobile-drawer__link">
            <?= htmlspecialchars($easyLabel) ?>
        </a>
    </nav>
</aside>

<?php if ($isLoggedIn): ?>
    <div id="gvAccountModal" class="gv-account-modal">
        <div class="gv-account-modal__panel">
            <button class="gv-account-modal__close" onclick="gvCloseModal('gvAccountModal')">
                <i class="fa fa-times-circle"></i>
            </button>

            <h2 class="gv-account-modal__title">Mon compte utilisateur</h2>
            <hr class="gv-account-modal__divider">

            <div class="gv-account-modal__profile">
                <img id="profil-pic" src="pictures/OIP.jpg" class="gv-account-modal__profile-img profil-pic-global" alt="Profil utilisateur">
                <input type="file" id="input-file" accept="image/png, image/jpeg, image/jpg" hidden>
                <label for="input-file" class="gv-account-modal__upload-btn">
                    Télécharger une image
                </label>
            </div>

            <hr class="gv-account-modal__divider">

            <div class="gv-account-modal__user">
                <p class="gv-account-modal__name"><?= htmlspecialchars($userFullName) ?></p>
                <p class="gv-account-modal__email"><?= htmlspecialchars($userEmail) ?></p>
            </div>

            <hr class="gv-account-modal__divider">

            <div class="gv-account-modal__actions">
                <button class="gv-account-modal__action gv-account-modal__action--danger" onclick="gvOpenModal('gvDeleteAccountModal')">
                    Supprimer
                </button>
                <button class="gv-account-modal__action gv-account-modal__action--neutral" onclick="gvOpenModal('gvLogoutModal')">
                    Se déconnecter
                </button>
                <button class="gv-account-modal__action gv-account-modal__action--primary">
                    Modifier
                </button>
            </div>
        </div>
    </div>

    <div id="gvDeleteAccountModal" class="gv-account-modal">
        <div class="gv-account-modal__panel">
            <button class="gv-account-modal__back" onclick="gvCloseModal('gvDeleteAccountModal')">
                <i class="fa fa-arrow-left"></i>
            </button>

            <h2 class="gv-account-modal__title">Suppression du compte</h2>
            <hr class="gv-account-modal__divider">

            <div class="gv-account-modal__profile">
                <img src="pictures/OIP.jpg" class="gv-account-modal__profile-img profil-pic-global" alt="Profil utilisateur">
            </div>

            <hr class="gv-account-modal__divider">

            <div class="gv-account-modal__user">
                <p class="gv-account-modal__name"><?= htmlspecialchars($userFullName) ?></p>
                <p class="gv-account-modal__email"><?= htmlspecialchars($userEmail) ?></p>
            </div>

            <hr class="gv-account-modal__divider">

            <p class="gv-account-modal__message">
                Attention, cette action effacera définitivement votre compte de l'application.
                Êtes-vous sûr de vouloir supprimer votre compte ?
            </p>

            <div class="gv-account-modal__actions">
                <button class="gv-account-modal__action gv-account-modal__action--danger">
                    Confirmer la suppression
                </button>
            </div>
        </div>
    </div>

    <div id="gvLogoutModal" class="gv-account-modal">
        <div class="gv-account-modal__panel">
            <button class="gv-account-modal__back" onclick="gvCloseModal('gvLogoutModal')">
                <i class="fa fa-arrow-left"></i>
            </button>

            <h2 class="gv-account-modal__title">Déconnexion</h2>
            <hr class="gv-account-modal__divider">

            <div class="gv-account-modal__profile">
                <img src="pictures/OIP.jpg" class="gv-account-modal__profile-img profil-pic-global" alt="Profil utilisateur">
            </div>

            <hr class="gv-account-modal__divider">

            <div class="gv-account-modal__user">
                <p class="gv-account-modal__name"><?= htmlspecialchars($userFullName) ?></p>
                <p class="gv-account-modal__email"><?= htmlspecialchars($userEmail) ?></p>
            </div>

            <hr class="gv-account-modal__divider">

            <p class="gv-account-modal__message">
                Attention, cette action vous déconnectera du site.
                Êtes-vous sûr de vouloir vous déconnecter ?
            </p>

            <div class="gv-account-modal__actions">
                <form method="post" class="gv-account-modal__form">
                    <button type="submit" name="deconnect_account" class="gv-account-modal__action gv-account-modal__action--neutral">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function gvOpenModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('is-open');
        }
    }

    function gvCloseModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('is-open');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const burgerButton = document.getElementById('gvBurgerButton');
        const closeDrawerButton = document.getElementById('gvCloseDrawer');
        const mobileDrawer = document.getElementById('gvMobileDrawer');
        const mobileBackdrop = document.getElementById('gvMobileBackdrop');

        function openDrawer() {
            if (mobileDrawer) mobileDrawer.classList.add('is-open');
            if (mobileBackdrop) mobileBackdrop.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            if (mobileDrawer) mobileDrawer.classList.remove('is-open');
            if (mobileBackdrop) mobileBackdrop.classList.remove('is-active');
            document.body.style.overflow = '';
        }

        if (burgerButton) {
            burgerButton.addEventListener('click', openDrawer);
        }

        if (closeDrawerButton) {
            closeDrawerButton.addEventListener('click', closeDrawer);
        }

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', closeDrawer);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDrawer();
                document.querySelectorAll('.gv-account-modal.is-open').forEach(function (modal) {
                    modal.classList.remove('is-open');
                });
            }
        });

        document.querySelectorAll('.gv-account-modal').forEach(function (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('is-open');
                }
            });
        });

        const profilPic = document.getElementById('profil-pic');
        const profilInput = document.getElementById('input-file');
        const profilOutils = document.getElementById('profil');
        const profilPics = document.querySelectorAll('.profil-pic-global');

        const savedImage = localStorage.getItem('savedImage');
        if (savedImage) {
            if (profilPic) profilPic.src = savedImage;
            if (profilOutils) profilOutils.src = savedImage;
            profilPics.forEach(img => img.src = savedImage);
        }

        if (profilInput) {
            profilInput.addEventListener('change', function () {
                const file = profilInput.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function (e) {
                    if (profilPic) profilPic.src = e.target.result;
                    if (profilOutils) profilOutils.src = e.target.result;
                    profilPics.forEach(img => img.src = e.target.result);
                    localStorage.setItem('savedImage', e.target.result);
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>