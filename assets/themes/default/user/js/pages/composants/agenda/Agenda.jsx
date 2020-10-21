import React, {Component} from 'react';
import Calendrier from '../../../../../react/functions/calendrier';
import {Page} from '../../../../../react/composants/page/Page';

export class Agenda extends Component {
    constructor (props){
        super ()

        this.state = {
            daySelected: JSON.parse(props.today),
            week: JSON.parse(props.week),
        }
    }
    render () {
        const {daySelected, week} = this.state

        let content = <div className="liste-agenda">
            <Week daySelected={daySelected} week={week}/>
        </div>

        return <>
            <Page content={content} />
        </>
    }
}

export class Week extends Component {
    render () {
        const {daySelected, week} = this.props

        let header = week.map((el, index) => {
            return <div className={"agenda-item" + (daySelected.wday == el.wday ? " active" : "")} key={index}>
                <div className="label">
                    <div className="label-desktop">{Calendrier.getDayFr(el.wday)}</div>
                    <div className="label-mobile">{Calendrier.getShortDayFr(el.wday)}</div>
                </div>
                <div className="number">{el.mday}</div>
            </div>
        })

        return <div className="agenda-table">
            <div className="agenda-table-header">
                {header}
            </div>
            <div className="agend-table-body">
                <div className="agenda-table-hours">

                </div>
                <div className="agenda-table-events">

                </div>
            </div>
        </div>
    }
}

