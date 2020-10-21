import '../../css/pages/agenda.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Agenda} from './composants/agenda/Agenda';

let el = document.getElementById("agenda");
if(el){
    ReactDOM.render(<Agenda week={el.dataset.week} />, el)
}