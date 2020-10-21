function getDayFr($day){
    $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

    return $days[$day];
}

function getShortDayFr($day){
    $days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

    return $days[$day];
}


module.exports = {
    getDayFr,
    getShortDayFr
}