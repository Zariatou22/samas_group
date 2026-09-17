/**
 * Découpe un texte pour respecter une longueur maximale par ligne.
 * * @param {string} texte - Le texte à traiter
 * @param {number} maxLongeur - La longueur maximale par ligne (défaut: 40)
 * @returns {string}
 */
function splitText(texte, maxLongeur = 40) {
    // On divise d'abord le texte en lignes existantes
    const lignesEntrees = texte.split("\n");
    const lignesSortie = [];

    for (let ligne of lignesEntrees) {
        // Tant que la ligne actuelle est plus longue que la limite
        while (ligne.length > maxLongeur) {
            // On cherche la position du dernier espace dans la limite autorisée (+1 pour le bord)
            const sousChaine = ligne.substring(0, maxLongeur + 1);
            const positionEspace = sousChaine.lastIndexOf(' ');

            if (positionEspace !== -1) {
                // CAS 1 : Un espace existe, on coupe proprement au mot
                lignesSortie.push(ligne.substring(0, positionEspace));
                ligne = ligne.substring(positionEspace + 1);
            } else {
                // CAS 2 : Pas d'espace (mot trop long), on coupe avec un tiret
                lignesSortie.push(ligne.substring(0, maxLongeur - 1) + '-');
                ligne = ligne.substring(maxLongeur - 1);
            }
        }
        // On ajoute le reliquat de la ligne
        lignesSortie.push(ligne);
    }

    return lignesSortie.join("\n");
}