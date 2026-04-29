import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'meuTemaDark',
    themes: {
      meuTemaDark: {
        dark: true,
        colors: {
          background: '#0f1117', // Fundo geral
          surface: '#16181f',    // Fundo dos cartões/sidebar
          primary: '#4f6ef7',    // Azul dos botões
          error: '#ef4444',      // Vermelho
          success: '#4ade80',    // Verde
          border: '#2a2d38'      // Cor das bordas
        }
      }
    }
  }
})

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(vuetify)

app.mount('#app')