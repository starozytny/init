import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Settings} from './composants/settings/Settings';

let el = document.getElementById("settings");
if(el){
    ReactDOM.render(
        <Settings isDanger={0} settings={el.dataset.settings}/>,
        el
    )
}