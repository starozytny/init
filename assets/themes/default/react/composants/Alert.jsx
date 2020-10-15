import React from 'react';

export function Alert({type, message, active}) {
    let icon;
    switch (type){
        case "danger":
            icon = 'warning'
            break;
        case "success":
            icon = 'check'
            break;
        case "warning":
            icon = 'warning'
            break;
        case "info":
            icon = 'information'
            break;
        default:
            icon = 'question'
            break;
    }


    return (
        <div className={'alert alert-' + type + (active ? ' active' : '')}>
            <span className={"icon-"+icon}></span>
            <span>{message}</span>
        </div>
    );
}