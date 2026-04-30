<template>
  <v-container fluid class="fill-height login-bg">
    <v-row justify="center" align="center">
      <v-col cols="12" sm="8" md="4" lg="3">
        <v-card class="pa-8 login-card" elevation="0">
          
          <div class="text-center mb-8">
            <div class="logo-text">AdminPanel</div>
            <div class="logo-sub">Acesso Restrito</div>
          </div>

          <v-alert v-if="errorMessage" type="error" variant="tonal" class="mb-6 rounded-lg text-body-2">
            {{ errorMessage }}
          </v-alert>

          <v-form @submit.prevent="handleLogin" :disabled="isLoading">
            <div class="field-label">E-mail</div>
            <v-text-field
              v-model="email"
              variant="outlined"
              bg-color="#0f1117"
              color="primary"
              class="mb-2"
              density="comfortable"
              required
            ></v-text-field>

            <div class="field-label">Senha</div>
            <v-text-field
              v-model="senha"
              type="password"
              variant="outlined"
              bg-color="#0f1117"
              color="primary"
              class="mb-6"
              density="comfortable"
              required
            ></v-text-field>

            <v-btn
              type="submit"
              block
              color="primary"
              height="44"
              class="text-none opt1-btn"
              :loading="isLoading"
              elevation="0"
            >
              Entrar no sistema
            </v-btn>
          </v-form>

        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const senha = ref('');

const isLoading = ref(false);
const errorMessage = ref('');

const handleLogin = async () => {
    isLoading.value = true;
    errorMessage.value = '';
    
    try {
        await authStore.login(email.value, senha.value);
        router.push('/');
    } catch (error: any) {
        errorMessage.value = error.response?.data?.message || 'Erro ao comunicar com o servidor.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.login-bg {
  background-color: #0f1117;
  font-family: 'DM Sans', sans-serif;
}

.login-card {
  background-color: #16181f !important;
  border: 1px solid #2a2d38;
  border-radius: 12px !important;
}

.logo-text {
  font-size: 22px;
  font-weight: 600;
  color: #fff;
  letter-spacing: -0.3px;
}

.logo-sub {
  font-size: 12px;
  color: #555;
  font-family: 'DM Mono', monospace;
  margin-top: 2px;
}

.field-label {
  font-size: 13px;
  color: #888;
  margin-bottom: 6px;
  font-family: 'DM Sans', sans-serif;
}

.opt1-btn {
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  letter-spacing: 0;
}

/* Força a estilização das bordas do input do Vuetify para bater com o seu design */
:deep(.v-field__outline__start), 
:deep(.v-field__outline__end), 
:deep(.v-field__outline__notch) {
  border-color: #2a2d38 !important;
}
</style>