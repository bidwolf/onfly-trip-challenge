import { createApp } from "vue";
import "./style.css";
import "./index.css";
import App from "./App.vue";
import router from "./router/index";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import api from "./services/api";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    disableStats: true,
    authEndpoint: `${import.meta.env.VITE_API_URL}/broadcasting/auth`,
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel: any, options: any) => {
        return {
            authorize: (socketId: string, callback: any) => {
                api.post(options.authEndpoint, {
                    socket_id: socketId,
                    channel_name: channel.name,
                }, {
                    withXSRFToken: true,
                }).then(
                    response => {
                        callback(false, response.data);
                    }
                ).catch(error => {
                    console.error('Authorization error:', error);
                    callback(true, { error: error });
                })
            }
        }
    }
});

createApp(App).use(router).mount("#app");
