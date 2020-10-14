import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import Loader from '../../../../../react/functions/loader';
import Validateur from '../../../../../react/functions/validateur';
import {Input} from '../../../../../react/composants/Fields';

export class Settings extends Component {
    constructor (props){
        super ()

        let emailGlobal = '', emailContact = '', emailRgpd = '';

        if(props.settings != undefined && props.settings != ''){
            let data = JSON.parse(JSON.parse(props.settings))
            let setting = data[0];
            emailGlobal = setting.emailGlobal
            emailContact = setting.emailContact
            emailRgpd = setting.emailRgpd
        }
        
        this.state = {
            emailGlobal: {value: emailGlobal, error: ''},
            emailContact: {value: emailContact, error: ''},
            emailRgpd: {value: emailRgpd, error: ''},
        }

        

        this.handleSubmit = this.handleSubmit.bind(this)
        this.handleChange = this.handleChange.bind(this)
    }

    handleChange = (e) => {
        this.setState({[ e.currentTarget.name]: {value: e.currentTarget.value}}) 
    }

    handleSubmit = (e) => {
        e.preventDefault()

        const {emailGlobal, emailContact, emailRgpd} = this.state

        let validate = Validateur.validateur([
            {type: "email", id: 'emailGlobal', value: emailGlobal.value},
            {type: "email", id: 'emailContact', value: emailContact.value},
            {type: "email", id: 'emailRgpd', value: emailRgpd.value}
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            Loader.loader(true)

            let self = this
            console.log(self.state)
            axios({ method: 'post', url: Routing.generate('super_settings_edit'), data: self.state }).then(function (response) {
                let data = response.data; let code = data.code; 
                if(code === 1){
                    location.reload()
                }else{
                    Loader.loader(false)
                    self.setState({error: data.message})
                }
            });
        }
    }

    render () {
        const {emailGlobal, emailContact, emailRgpd} = this.state
        const {isDanger} = this.props

        return <>
            <div className={"card-dash " + (isDanger == 1 ? 'card-dash-danger' : '')}>
                <div className="card-body">
                    {isDanger == 1 ? <span className="txt-alpha"><span className="icon-warning"></span>Veuillez configurer les paramètres du sites.</span> : null}
                    
                    <form onSubmit={this.handleSubmit}>
                        <div className="line line-3">
                            <Input type="email" identifiant="emailGlobal" valeur={emailGlobal} onChange={this.handleChange}>E-mail expéditeur global</Input>
                            <Input type="email" identifiant="emailContact" valeur={emailContact} onChange={this.handleChange}>E-mail destinataire contact</Input>
                            <Input type="email" identifiant="emailRgpd" valeur={emailRgpd} onChange={this.handleChange}>E-mail destinataire RGPD</Input>
                        </div>
                        <div className="form-button">
                            <button type="submit" className="btn btn-primary"><span>Valider</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    }
}