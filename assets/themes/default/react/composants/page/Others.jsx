import React, {Component} from 'react';

export class Others extends Component {
    render () {
        const {haveExport, urlExportExcel, urlExportCsv, nameExport, haveImport} = this.props

        return <>
            <div className="others">
                <div className="others-left"></div>
                <div className="others-right">
                    {haveImport ? <div className="others-item"><a className="btn"><span>Importer</span></a></div> : null}
                    {haveExport ? <div className="others-item"><a className="btn" href={urlExportCsv} download={nameExport + ".csv"}><span>Exporter CSV</span></a></div> : null}
                    {haveExport ? <div className="others-item"><a className="btn" href={urlExportExcel} download={nameExport + ".xlsx"}><span>Exporter Excel</span></a></div> : null}
                </div>
            </div>
        </>
    }
}