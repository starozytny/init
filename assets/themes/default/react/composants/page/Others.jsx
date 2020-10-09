import React, {Component} from 'react';
import {Aside} from './Aside';
import axios from 'axios/dist/axios';
import Loader from '../../functions/loader';
import {Radiobox} from '../Fields'
import {Alert} from '../Alert';
import {Drop} from '../Drop';

export class Others extends Component {
    constructor (props) {
        super()

        this.aside = React.createRef();

        this.handleOpenImport = this.handleOpenImport.bind(this);
    }

    handleOpenImport = (e) => {
        e.preventDefault();
        this.aside.current.handleUpdate('Importer');
    }

    render () {
        const {haveExport, urlExportExcel, urlExportCsv, nameExport, haveImport, urlImport} = this.props

        let asideContent = <AsideImport urlForm={urlImport}/>

        return <>
            <div className="others">
                <div className="others-left"></div>
                <div className="others-right">
                    {haveImport ? <div className="others-item"><a className="btn" onClick={this.handleOpenImport}><span>Importer</span></a></div> : null}
                    {haveExport ? <div className="others-item"><a className="btn" href={urlExportCsv} download={nameExport + ".csv"}><span>Exporter CSV</span></a></div> : null}
                    {haveExport ? <div className="others-item"><a className="btn" href={urlExportExcel} download={nameExport + ".xlsx"}><span>Exporter Excel</span></a></div> : null}
                </div>
            </div>
            {haveImport ? <Aside content={asideContent} ref={this.aside}/> : null}
        </>
    }
}

export class AsideImport extends Component {
    constructor (props) {
        super()

        this.state = {
            error: '',
            anomalies: [],
            urlAnomalie: null,
            filenameAnomalie: '',
            choices: {value: 0, error: ''},
            file: ''
        }
        this.handleSubmit = this.handleSubmit.bind(this)
        this.handleGetFile = this.handleGetFile.bind(this)
        this.handleChange = this.handleChange.bind(this)
    }

    handleChange = (e) => { 
        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        this.setState({[name]: {value: value, error: ''}}) 
    }

    handleGetFile = (e) => { this.setState({file: e.file}) }

    handleSubmit = (e) => {
        e.preventDefault()

        const {file, choices} = this.state
        const {urlForm} = this.props

        Loader.loader(true)
        let fd = new FormData();
        fd.append('file', file);
        fd.append('choice', choices.value);
        fd.append('poursuivre', document.getElementsByName('poursuivre').value)

        console.log(document.getElementsByName('poursuivre').value)
        
        let self = this
        axios({ method: 'post', url: urlForm, data: fd, headers: {'Content-Type': 'multipart/form-data'} }).then(function (response) {
            let data = response.data; let code = data.code; Loader.loader(false);
            if (code === 1) {
                location.reload()
            }else{
                self.setState({error: data.message})

                if(data.anomalies){
                    self.setState({anomalies: data.anomalies, urlAnomalie: data.urlAnomalie, filenameAnomalie: data.filename})
                }
            }
        });
    }

    render () {
        const {error, choices, anomalies, urlAnomalie, filenameAnomalie} = this.state

        let choiceItems = [
            { 'id': 1, 'value': 0, 'label': 'Ne pas écraser les données', 'identifiant': 'ne-pas-ecraser', 'checked': false },
            { 'id': 2, 'value': 1, 'label': 'Ecraser les données', 'identifiant': 'ecraser', 'checked': false },
        ]

        choiceItems.map(el => {
            if (choices.value == el.value){ el.checked = true }
        })

        let anomaliesItems = anomalies.map((el, index) => {
            return <div key={index} className="anomalie">
                <div className="anomalie-id">{el.id != "" ? el.id : "#"}</div>
                <div className="anomalie-username">{el.username != "" ? el.username : "#"}</div>
                <div className="anomalie-email">{el.email != "" ? el.email : "#"}</div>
            </div>
        })

        return <>
            <div className="aside-user-informations">
                <p>Fichier CSV ayant dans ses champs les propriétés suivantes : (id, username, email)</p>
                <div className="alert alert-warning">
                    <i>Ecraser les données</i> se fait en fonction de l'id et/ou username et/ou email. <br/>
                    Ces trois propriétés doivent être unique.
                </div>
            </div>
            <form className="aside-user-form" onSubmit={this.handleSubmit}>
                {error != '' ? <Alert type="danger" message={error} active="true" /> : null}
                {anomalies.length != 0 ? <>
                    <div className="anomalies">
                        {anomaliesItems}
                    </div>
                    <input type="hidden" name="poursuivre" value="1" />
                    <div className="form-button">
                        <a href={urlAnomalie} download={filenameAnomalie} className="btn btn-primary"><span>Télécharger les doublons</span></a>
                        <button type="submit" className="btn btn-danger"><span>Poursuivre l'import</span></button>
                    </div>
                </>
                : <>
                <div className="line">
                    <label>Fichier au format CSV</label>
                    <div className="form-files">
                        <Drop label="Téléverser le fichier" labelError="Seul les fichiers au format CSV sont acceptées."
                            accept={".csv"} maxFiles={1} onGetFile={this.handleGetFile}/>
                    </div>
                </div>
                <div className="line">
                    <Radiobox items={choiceItems} name="choices" valeur={choices} onChange={this.handleChange}></Radiobox>
                </div>
                <div className="form-button">
                    <button type="submit" className="btn btn-primary"><span>Importer</span></button>
                </div>
                </> }
                
            </form>
        </>
    }
}