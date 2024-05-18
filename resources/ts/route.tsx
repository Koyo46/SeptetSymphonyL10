import React from 'react';
import { createRoot } from 'react-dom/client';
import {BrowserRouter, Route, Routes} from 'react-router-dom';
import Home from './Pages/Home';
import SettingGame from './Pages/SettingGame';
import GameTable from './Pages/GameTable';
import GamePlay from './Pages/GamePlay';

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path='/'  element={<SettingGame />} />
                <Route path='/gameTable/:gameId/:playerCount'  element={<GameTable />} />
                <Route path='/gamePlay/:gameId'  element={<GamePlay />} />
            </Routes>
        </BrowserRouter>
    );
}

const container = document.getElementById('app');
const root = createRoot(container!);
root.render(<App />);