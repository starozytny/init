import React from 'react';
import Routing from '../../../../../public/bundles/fosjsrouting/js/router.min.js';
import Trumbowyg from 'react-trumbowyg';
import 'react-trumbowyg/dist/trumbowyg.min.css';
import '../../../../../node_modules/trumbowyg/dist/plugins/base64/trumbowyg.base64';
import '../../../../../node_modules/trumbowyg/dist/plugins/cleanpaste/trumbowyg.cleanpaste';
import '../../../../../node_modules/trumbowyg/dist/plugins/colors/trumbowyg.colors';
import '../../../../../node_modules/trumbowyg/dist/plugins/colors/ui/sass/trumbowyg.colors.scss';
import '../../../../../node_modules/trumbowyg/dist/plugins/fontsize/trumbowyg.fontsize';
import '../../../../../node_modules/trumbowyg/dist/plugins/pasteimage/trumbowyg.pasteimage';
import '../../../../../node_modules/trumbowyg/dist/plugins/history/trumbowyg.history';
import '../../../../../node_modules/trumbowyg/dist/plugins/upload/trumbowyg.upload';
import '../functions/textarea/plugins/trumbowyg.alert';
import DatePicker from "react-datepicker";
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr)
import "react-datepicker/dist/react-datepicker.css";

export function Input({type="text", identifiant, valeur, onChange, children, placeholder}) {
    return (
        <div className={'form-group' + (valeur.error ? " form-group-error" : "")}>
            <label htmlFor={identifiant}>{children}</label>
            <input type={type} name={identifiant} id={identifiant} placeholder={placeholder} value={valeur.value} onChange={onChange}/>
            <div className="error">{valeur.error ? <><span className='icon-warning'></span>{valeur.error}</> : null}</div>
        </div>
    );
}

export function Checkbox({items, name, valeur, onChange, children}) {
    let itemsInputs = items.map(elem => {
        return <div className={"checkbox-item " + (elem.checked ? 'checked' : '')} key={elem.id}>
            <label htmlFor={elem.identifiant}>
                {elem.label}
                <input type="checkbox" name={name} id={elem.identifiant} value={elem.value} checked={elem.checked ? 'checked' : ''} onChange={onChange}/>
            </label>
        </div>
    })

    return (
        <div className={'form-group form-group-checkbox' + (valeur.error ? " form-group-error" : "")}>
            <label>{children}</label>
            <div className="checkbox-items">
                {itemsInputs}
            </div>
            <div className="error">{valeur.error ? <><span className='icon-warning'></span>{valeur.error}</> : null}</div>
        </div>
    );
}

export function Radiobox({items, name, valeur, onChange, children}) {
    let itemsInputs = items.map(elem => {
        return <div className={"radiobox-item " + (elem.checked ? 'checked' : '')} key={elem.id}>
            <label htmlFor={elem.identifiant}>
                <span>{elem.label}</span>
                <input type="radio" name={name} id={elem.identifiant} value={elem.value} checked={elem.checked ? 'checked' : ''} onChange={onChange}/>
            </label>
        </div>
    })

    return (
        <div className={'form-group form-group-radiobox' + (valeur.error ? " form-group-error" : "")}>
            <label>{children}</label>
            <div className="radiobox-items">
                {itemsInputs}
            </div>
            <div className="error">{valeur.error ? <><span className='icon-warning'></span>{valeur.error}</> : null}</div>
        </div>
    );
}

export function TextArea({identifiant, valeur, onChange, children}) {
    return (
        <div className={'form-group' + (valeur.error ? " form-group-error" : "")}>
            <label htmlFor={identifiant}>{children}</label>
            <textarea name={identifiant} id={identifiant} value={valeur.value} rows="8" onChange={onChange}/>
            <div className="error">{valeur.error ? <><span className='icon-warning'></span>{valeur.error}</> : null}</div>
        </div>
    );
}

export function TextAreaWys({identifiant, valeur, onChange, reference, children}){
    return (
        <div className={'form-group' + (valeur.error ? " form-group-error" : "")}>
            <label htmlFor={identifiant}>{children}</label>
            <Trumbowyg id='react-trumbowyg'
                        buttons={
                            [
                                ['viewHTML'],
                                ['historyUndo', 'historyRedo'],
                                ['formatting'],
                                ['fontsize'],
                                'btnGrp-semantic',
                                ['link'],
                                ['insertImage'],
                                ['upload'],
                                ['base64'],
                                ['foreColor', 'backColor'],
                                'btnGrp-justify',
                                'btnGrp-lists',
                                ['horizontalRule'],
                                ['alert'],
                                ['fullscreen']
                            ]
                        }
                        data={valeur.value}
                        placeholder=''
                        onChange={onChange}
                        ref={reference}
                        plugins= {{
                            upload: {
                                serverPath: Routing.generate('admin_doc_guide_upload'),
								fileFieldName: 'image',
                                urlPropertyName: 'data.link'
                            }
                        }}
                    />
            <div className="error">{valeur.error ? <><span className='icon-warning'></span>{valeur.error}</> : null}</div>
        </div>
    );
}

export function Select({identifiant, valeur, onChange, children, items}) {
    let choices = items.map((item) => 
        <option key={item.value} value={item.value}>{item.libelle}</option>
    )
    return (
        <div className={'form-group' + (valeur.error ? " form-group-error" : "")}>
            <label>
                {children}
                <select value={valeur.value} id={identifiant} name={identifiant} onChange={onChange}>
                    {choices}
                </select>
            </label>
            <div className="error">{valeur.error ? <><span className='icon-warning'></span>{valeur.error}</> : null}</div>            
        </div>
    );
}

export function DatePick({valeur, onChange, children, minDate="", maxDate="", format="dd/MM/yyyy", placeholder="DD/MM/YYYY"}){
    return (
        <div className={'form-group-date form-group' + (valeur.error ? " form-group-error" : "")}>
            <label>{children}</label>
            <DatePicker
                locale="fr"
                selected={valeur.inputVal}
                onChange={onChange}
                dateFormat={format}
                peekNextMonth
                showMonthDropdown
                showYearDropdown
                dropdownMode="select"
                placeholderText={placeholder}
                minDate={minDate}
                maxDate={maxDate}
                />
            <div className='error'>{valeur.error ? valeur.error : null}</div>
        </div>
    )
}

export function DateTimePick({valeur, onChange, children, minDate="", maxDate="", format="dd/MM/yyyy", placeholder="DD/MM/YYYY", timeFormat="HH:mm", timeIntervals=15}){
    return (
        <div className={'form-group-date form-group' + (valeur.error ? " form-group-error" : "")}>
            <label>{children}</label>
            <DatePicker
                locale="fr"
                selected={valeur.inputVal}
                onChange={onChange}
                dateFormat={format}
                timeFormat={timeFormat}
                timeIntervals={timeIntervals}
                peekNextMonth
                showMonthDropdown
                showYearDropdown
                showTimeSelect
                dropdownMode="select"
                placeholderText={placeholder}
                minDate={minDate}
                maxDate={maxDate}
                />
            <div className='error'>{valeur.error ? valeur.error : null}</div>
        </div>
    )
}

export function Switcher({identifiant, valeur, children, isChecked, onChange}){
    return (
        <div className={'form-group' + (valeur.error ? " form-group-error" : "")}>
            <label>{children}</label>
            <div className="toggle-wrapper">
                <div className="toggle checkcross">
                    <input id={identifiant} name={identifiant} checked={isChecked ? 'checked' : ''} className="input-checkcross" onChange={onChange} type="checkbox"/>
                    <label className="toggle-item" htmlFor={identifiant}>
                        <div className="check"></div>
                    </label>
                </div>
            </div>
            <div className="error">{valeur.error ? <><span className='icon-warning'></span>{valeur.error}</> : null}</div>
        </div>
    )
}