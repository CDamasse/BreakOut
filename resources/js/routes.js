import VueRouter from 'vue-router';
// Vues
import Home from './components/Home.vue'
import Shop from './components/Shop';
import Rooms from "./components/Rooms";

//Vue utilisateur
import Account from './components/parameters/Account';
import Avatar from './components/parameters/Avatar';
import Inventory from './components/parameters/Inventory';
import Myrooms from './components/parameters/Myrooms';
import Gemclicker from './components/hidden/Gemclicker'

// Salles de jeu
import CristalInfini from './components/rooms/CristalInfini'

//Vue d'erreurs
import PageNotFound from './components/errors/PageNotFound'

const router = new VueRouter({
    mode: 'history',
    routes : [
        { path: '/', component: Home},
        { path: '/home', component: Home},
        { path: '/rooms', component: Rooms},
        { path: '/shop', component: Shop},
        { path: '/account', component: Account},
        { path: '/avatar', component: Avatar},
        { path: '/inventory', component: Inventory},
        { path: '/myrooms', component: Myrooms},
        { path: '/gemclicker', component: Gemclicker},
        { path: '/play/cristal-infini', component: CristalInfini},
        { path: '*', component: PageNotFound},

    ]
});

export default router
