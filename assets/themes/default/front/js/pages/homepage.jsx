import '../../css/pages/homepage.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import Compteur from '../components/composants/Compteur';

experience();

function experience(){
    let y = new Date();
    let el = document.getElementById('r-compteur');
    if(el){
        ReactDOM.render(
            <Compteur max={y.getFullYear() - parseInt(document.querySelector('#r-compteur').dataset.count)}  timer="25"/>,
            
        );
    }
}