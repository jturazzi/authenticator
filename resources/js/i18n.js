import { ref } from 'vue';

const locale = ref(localStorage.getItem('locale') || 'fr');

const messages = {
  fr: {
    // Nav
    'nav.myAccounts': 'Mes comptes',
    'nav.admin': 'Administration',
    'nav.changeTheme': 'Changer le thème',
    'nav.logout': 'Déconnexion',

    // Login
    'login.title': 'Connexion',
    'login.appName': 'Authenticator',
    'login.subtitle': 'Gérez vos codes d\'authentification à double facteur',
    'login.microsoft': 'Se connecter avec Microsoft 365',

    // Dashboard
    'dashboard.title': 'Mes codes d\'authentification',
    'dashboard.headTitle': 'Mes codes',
    'dashboard.scan': 'Scanner',
    'dashboard.add': 'Ajouter',
    'dashboard.noAccounts': 'Aucun compte TOTP',
    'dashboard.noAccountsDesc': 'Ajoutez votre premier compte d\'authentification à double facteur.',
    'dashboard.scanQr': 'Scanner un QR Code',
    'dashboard.addManually': 'Ajouter manuellement',
    'dashboard.details': 'Détails',
    'dashboard.deleteLocked': 'Suppression verrouillée',
    'dashboard.confirmDelete': 'Supprimer ce compte TOTP ?',

    // Common
    'common.copied': 'Copié !',
    'common.back': '← Retour',
    'common.delete': 'Supprimer',

    // Create
    'create.title': 'Ajouter un compte',
    'create.heading': 'Ajouter un compte TOTP',
    'create.submit': 'Ajouter le compte',

    // Form
    'form.accountName': 'Nom du compte *',
    'form.description': 'Description',
    'form.issuer': 'Émetteur',
    'form.secretOptional': 'Clé secrète (optionnel)',
    'form.secretRequired': 'Clé secrète *',
    'form.leaveEmpty': 'Laissez vide pour générer automatiquement',
    'form.pasteSecret': 'Si vous avez une clé secrète, collez-la ici.',
    'form.descriptionHelp': 'Un nom personnalisé pour identifier facilement ce compte.',
    'form.digits': 'Digits',
    'form.period': 'Période (s)',
    'form.algorithm': 'Algorithme',
    'form.placeholderName': 'ex: user@example.com',
    'form.placeholderDesc': 'ex: Google, GitHub, Microsoft...',
    'form.placeholderIssuer': 'ex: Google, GitHub, Microsoft...',

    // Scan
    'scan.title': 'Scanner un QR Code',
    'scan.startCamera': 'Démarrer la caméra',
    'scan.stop': 'Arrêter',
    'scan.detected': 'QR code détecté',
    'scan.accountLabel': 'Nom à donner au compte',
    'scan.submit': 'Ajouter ce compte',
    'scan.cameraError': 'Impossible d\'accéder à la caméra: ',
    'scan.placeholderDesc': 'Ex: Google, GitHub, Microsoft...',

    // Show
    'show.qrCode': 'QR Code',
    'show.scanWith': 'Scannez avec une autre application authenticator',
    'show.scanWithAdmin': 'Scannez avec une application authenticator',
    'show.description': 'Description',
    'show.algo': 'Algo',
    'show.digits': 'Digits',
    'show.period': 'Période',
    'show.seconds': 'secondes',
    'show.edit': 'Modifier',

    // Admin
    'admin.title': 'Administration des utilisateurs',
    'admin.user': 'Utilisateur',
    'admin.email': 'Email',
    'admin.totpAccounts': 'Comptes TOTP',
    'admin.role': 'Rôle',
    'admin.actions': 'Actions',
    'admin.viewAccounts': 'Voir comptes',
    'admin.removeAdmin': 'Retirer admin',
    'admin.makeAdmin': 'Rendre admin',
    'admin.view': 'Voir',
    'admin.confirmRoleChange': 'Changer le rôle de {name} ?',
    'admin.confirmDeleteUser': 'Supprimer l\'utilisateur {name} ? Cette action est irréversible.',
    'admin.account': 'compte',
    'admin.accounts': 'comptes',
    'admin.createdAt': 'Création',
    'admin.lastLogin': 'Dernier login',
    'admin.never': 'Jamais',

    // Admin UserAccounts
    'userAccounts.title': 'Comptes TOTP de {name}',
    'userAccounts.addTotp': 'Ajouter un compte TOTP',
    'userAccounts.noAccounts': 'Aucun compte TOTP pour cet utilisateur.',
    'userAccounts.unlock': 'Déverrouiller la suppression',
    'userAccounts.lock': 'Verrouiller la suppression',
    'userAccounts.deleteLocked': 'Suppression verrouillée',

    // Admin Create/Edit
    'adminCreate.heading': 'Ajouter un compte TOTP',
    'adminCreate.forUser': 'Pour {name} ({email})',
    'edit.heading': 'Modifier le compte TOTP',
    'edit.submit': 'Enregistrer les modifications',

    // Footer
    'footer.terms': 'CGU',
    'footer.createdBy': 'Créé par',

    // Terms page
    'terms.title': 'Conditions Générales d\'Utilisation',
    'terms.section1Title': 'Objet',
    'terms.section1Text': 'Authenticator est une application interne de gestion de codes d\'authentification à double facteur (TOTP). Elle permet aux utilisateurs autorisés de stocker, générer et consulter leurs codes TOTP de manière sécurisée.',
    'terms.section2Title': 'Accès au service',
    'terms.section2Text': 'L\'accès à l\'application est réservé aux utilisateurs authentifiés via Microsoft 365. Toute utilisation non autorisée est strictement interdite.',
    'terms.section3Title': 'Responsabilités',
    'terms.section3Text': 'Les utilisateurs sont responsables de la confidentialité de leurs comptes TOTP. L\'administrateur ne saurait être tenu responsable de toute perte de données résultant d\'une mauvaise utilisation du service.',
    'terms.section4Title': 'Données personnelles',
    'terms.section4Text': 'Les données stockées se limitent aux informations d\'authentification Microsoft 365 (nom, email) et aux comptes TOTP enregistrés. Aucune donnée n\'est partagée avec des tiers.',
    'terms.section5Title': 'Modifications',
    'terms.section5Text': 'Ces conditions peuvent être modifiées à tout moment. Les utilisateurs seront informés de toute modification substantielle.',
  },
  en: {
    // Nav
    'nav.myAccounts': 'My accounts',
    'nav.admin': 'Administration',
    'nav.changeTheme': 'Change theme',
    'nav.logout': 'Logout',

    // Login
    'login.title': 'Login',
    'login.appName': 'Authenticator',
    'login.subtitle': 'Manage your two-factor authentication codes',
    'login.microsoft': 'Sign in with Microsoft 365',

    // Dashboard
    'dashboard.title': 'My authentication codes',
    'dashboard.headTitle': 'My codes',
    'dashboard.scan': 'Scan',
    'dashboard.add': 'Add',
    'dashboard.noAccounts': 'No TOTP accounts',
    'dashboard.noAccountsDesc': 'Add your first two-factor authentication account.',
    'dashboard.scanQr': 'Scan a QR Code',
    'dashboard.addManually': 'Add manually',
    'dashboard.details': 'Details',
    'dashboard.deleteLocked': 'Deletion locked',
    'dashboard.confirmDelete': 'Delete this TOTP account?',

    // Common
    'common.copied': 'Copied!',
    'common.back': '← Back',
    'common.delete': 'Delete',

    // Create
    'create.title': 'Add account',
    'create.heading': 'Add a TOTP account',
    'create.submit': 'Add account',

    // Form
    'form.accountName': 'Account name *',
    'form.description': 'Description',
    'form.issuer': 'Issuer',
    'form.secretOptional': 'Secret key (optional)',
    'form.secretRequired': 'Secret key *',
    'form.leaveEmpty': 'Leave empty to generate automatically',
    'form.pasteSecret': 'If you have a secret key, paste it here.',
    'form.descriptionHelp': 'A custom name to easily identify this account.',
    'form.digits': 'Digits',
    'form.period': 'Period (s)',
    'form.algorithm': 'Algorithm',
    'form.placeholderName': 'e.g.: user@example.com',
    'form.placeholderDesc': 'e.g.: Google, GitHub, Microsoft...',
    'form.placeholderIssuer': 'e.g.: Google, GitHub, Microsoft...',

    // Scan
    'scan.title': 'Scan a QR Code',
    'scan.startCamera': 'Start camera',
    'scan.stop': 'Stop',
    'scan.detected': 'QR code detected',
    'scan.accountLabel': 'Account name',
    'scan.submit': 'Add this account',
    'scan.cameraError': 'Cannot access camera: ',
    'scan.placeholderDesc': 'E.g.: Google, GitHub, Microsoft...',

    // Show
    'show.qrCode': 'QR Code',
    'show.scanWith': 'Scan with another authenticator app',
    'show.scanWithAdmin': 'Scan with an authenticator app',
    'show.description': 'Description',
    'show.algo': 'Algo',
    'show.digits': 'Digits',
    'show.period': 'Period',
    'show.seconds': 'seconds',
    'show.edit': 'Edit',

    // Admin
    'admin.title': 'User administration',
    'admin.user': 'User',
    'admin.email': 'Email',
    'admin.totpAccounts': 'TOTP accounts',
    'admin.role': 'Role',
    'admin.actions': 'Actions',
    'admin.viewAccounts': 'View accounts',
    'admin.removeAdmin': 'Remove admin',
    'admin.makeAdmin': 'Make admin',
    'admin.view': 'View',
    'admin.confirmRoleChange': 'Change role of {name}?',
    'admin.confirmDeleteUser': 'Delete user {name}? This action is irreversible.',
    'admin.account': 'account',
    'admin.accounts': 'accounts',
    'admin.createdAt': 'Created',
    'admin.lastLogin': 'Last login',
    'admin.never': 'Never',

    // Admin UserAccounts
    'userAccounts.title': 'TOTP accounts of {name}',
    'userAccounts.addTotp': 'Add a TOTP account',
    'userAccounts.noAccounts': 'No TOTP accounts for this user.',
    'userAccounts.unlock': 'Unlock deletion',
    'userAccounts.lock': 'Lock deletion',
    'userAccounts.deleteLocked': 'Deletion locked',

    // Admin Create/Edit
    'adminCreate.heading': 'Add a TOTP account',
    'adminCreate.forUser': 'For {name} ({email})',
    'edit.heading': 'Edit TOTP account',
    'edit.submit': 'Save changes',

    // Footer
    'footer.terms': 'Terms',
    'footer.createdBy': 'Created by',

    // Terms page
    'terms.title': 'Terms of Use',
    'terms.section1Title': 'Purpose',
    'terms.section1Text': 'Authenticator is an internal application for managing two-factor authentication (TOTP) codes. It allows authorized users to securely store, generate, and view their TOTP codes.',
    'terms.section2Title': 'Access',
    'terms.section2Text': 'Access to the application is restricted to users authenticated via Microsoft 365. Any unauthorized use is strictly prohibited.',
    'terms.section3Title': 'Responsibilities',
    'terms.section3Text': 'Users are responsible for the confidentiality of their TOTP accounts. The administrator cannot be held responsible for any data loss resulting from misuse of the service.',
    'terms.section4Title': 'Personal Data',
    'terms.section4Text': 'Stored data is limited to Microsoft 365 authentication information (name, email) and registered TOTP accounts. No data is shared with third parties.',
    'terms.section5Title': 'Changes',
    'terms.section5Text': 'These terms may be modified at any time. Users will be informed of any substantial changes.',
  },
};

export function useI18n() {
  function t(key, params = {}) {
    let value = messages[locale.value]?.[key] || messages.fr[key] || key;
    for (const [k, v] of Object.entries(params)) {
      value = value.replace(`{${k}}`, v);
    }
    return value;
  }

  function setLocale(l) {
    locale.value = l;
    localStorage.setItem('locale', l);
  }

  return { t, locale, setLocale };
}
