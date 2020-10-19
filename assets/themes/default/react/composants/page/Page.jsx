import React, {Component} from 'react';
import {Pagination} from '../Pagination';
import {Toolbar} from './Toolbar.jsx';
import {Others} from './Others.jsx';

export class Page extends Component {
    constructor (props) {
        super(props)
    }

    render () {
        const {infos, content, 
            havePagination, perPage, taille, itemsPagination, 
            haveSearch, onSearch,
            haveAdd, onAdd,
            haveExport, urlExportExcel, urlExportCsv, nameExport,
            haveImport, asideImport,
            haveAllDelete, onAllDelete
        } = this.props

        return <>
            {infos}
            <Toolbar haveSearch={haveSearch} onSearch={onSearch} haveAdd={haveAdd} onAdd={onAdd}/>
            {content}
            {havePagination ? <Pagination perPage={perPage} taille={taille} items={itemsPagination} onUpdate={(items) => this.props.onUpdate(items)}/> : null}
            <Others haveExport={haveExport} urlExportExcel={urlExportExcel} urlExportCsv={urlExportCsv} nameExport={nameExport} 
                    haveImport={haveImport} asideImport={asideImport}
                    haveAllDelete={haveAllDelete} onAllDelete={onAllDelete}/>
        </>
    }
}