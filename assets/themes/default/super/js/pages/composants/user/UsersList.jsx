import React, {Component} from 'react';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';

export class UsersList extends Component {
    constructor (props) {
        super()
        
        this.handleOpenAside = this.handleOpenAside.bind(this)
        this.handleConvert = this.handleConvert.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
    }

    handleOpenAside = (e) => { this.props.onOpenAside(e.currentTarget.dataset.id) }
    handleConvert = (e) => { this.props.onConvertIsNew(e.currentTarget.dataset.id) }
    handleDelete = (e) => { this.props.onDelete(e.currentTarget.dataset.id) }

    render () {
        const {users} = this.props

        let items = users.map(elem => {

            let impersonate = Routing.generate('super_users_index', {'_switch_user': elem.username})
            if(elem.highRoleCode == 2){
                impersonate = Routing.generate('admin_dashboard', {'_switch_user': elem.username})
            }else if(elem.highRoleCode == 0){
                impersonate = Routing.generate('app_homepage', {'_switch_user': elem.username})
            }

            return <div className="item-user" key={elem.id}>
                <div className="item-user-actions">
                    <div className="user-selector">
                        {elem.highRoleCode != 0 ? <div className="item-user-roles"><div className={"user-badge user-badge-" + elem.highRoleCode}>{elem.highRole}</div></div> : null}
                    </div>
                    <div className="user-actions">
                        <span className="icon-more"></span>
                        <div className="user-actions-drop">
                            <div className="drop-items">
                                <span className="drop-item" onClick={this.handleOpenAside} data-id={elem.id}>Modifier</span>
                                {elem.highRoleCode != 1 ? <span className="drop-item" onClick={this.handleDelete} data-id={elem.id}>Supprimer</span> : null}
                                <a className="drop-item" href={impersonate}>Impersonate</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="item-user-avatar" onClick={this.handleOpenAside} data-id={elem.id}>
                    <img src={"../../uploads/" + elem.avatar} alt={"avatar de " + elem.username} />
                </div>
                <div className="item-user-username" onClick={this.handleOpenAside} data-id={elem.id}>
                    {elem.isNew ? <><div className="user-new btn-icon" onClick={this.handleConvert} data-id={elem.id}><span className="icon-unlock"></span><span className="icon-padlock"></span><span className="tooltip">Débloquer</span></div></> : null}          
                    <span>{elem.username}</span>
                </div>
                <div className="item-user-email">{elem.email}</div>   
            </div>
        })

        return <> {items} </>
    }
}