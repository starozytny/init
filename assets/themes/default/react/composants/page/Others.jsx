import React, {Component} from 'react';
import {Aside} from './Aside';

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
        const {haveExport, urlExportExcel, urlExportCsv, nameExport, haveImport, asideImport, haveAllDelete} = this.props

        return <>
            <div className="others">
                <div className="others-left">
                    <div className="title">Actions sur les sélectionnés : </div>
                    {haveAllDelete ? <div className="others-item"><a className="btn"><span>Supprimer</span></a></div> : null}
                </div>
                <div className="others-right">
                    <div className="title">Actions globales : </div>
                    <div className="others-items">
                        {haveImport ? <div className="others-item"><a className="btn" onClick={this.handleOpenImport}><span>Importer</span></a></div> : null}
                        {haveExport ? <div className="others-item"><a className="btn" href={urlExportCsv} download={nameExport + ".csv"}><span>Exporter CSV</span></a></div> : null}
                        {haveExport ? <div className="others-item"><a className="btn" href={urlExportExcel} download={nameExport + ".xlsx"}><span>Exporter Excel</span></a></div> : null}
                    </div>
                </div>
            </div>
            {haveImport ? <Aside content={asideImport} ref={this.aside}/> : null}
        </>
    }
}

