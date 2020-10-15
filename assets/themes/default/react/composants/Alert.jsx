import React from 'react';

export function Alert({type, message, active, children}) {
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
        case "question":
            icon = 'question'
            break;
        default:
            break;
    }


    return (
        <div className={'alert alert-' + type + (active ? ' active' : '')}>
            {icon ? <span className={"icon-"+icon}></span> : null}
            {message ? <span>{message}</span> : null}
            {children ? <p>{children}</p> : null}
        </div>
    );
}