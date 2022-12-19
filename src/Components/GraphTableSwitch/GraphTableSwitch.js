import React from 'react';
import './GraphTableSwitch.scss';

function GraphTableSwitch({ grapOrTable, handleSwitchChange, name }) {
    return (
        <form className="graph-table-switch" onChange={handleSwitchChange}>
            <label htmlFor="graph" className={grapOrTable === 'graph' ? 'graph-active' : ''}>
                <span>Graph</span>
                <input type="radio" id="graph" name={name} value="graph" />
            </label>

            <label htmlFor="table" className={grapOrTable === 'table' ? 'table-active' : ''}>
                <span>Table</span>
                <input type="radio" id="table" name={name} value="table" />
            </label>
        </form>
    );
}

export default GraphTableSwitch;
