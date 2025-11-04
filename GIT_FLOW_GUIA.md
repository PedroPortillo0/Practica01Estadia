# 🌊 Guía de Git Flow - ActividadEstadia

## 📊 Estructura de Ramas

```
main (producción)
  └─ Código estable en producción
  └─ ⚠️ NUNCA trabajar directamente aquí

develop (integración)
  └─ Rama de desarrollo principal
  └─ Aquí se integran todas las features
  
feature/* (funcionalidades)
  └─ feature/nombre-funcionalidad
  └─ Se crean desde develop
  └─ Se eliminan después del merge
  
hotfix/* (correcciones urgentes)
  └─ hotfix/nombre-bug
  └─ Se crean desde main
```

---

## 🚀 Flujo de Trabajo Diario

### 1️⃣ **Iniciar Nueva Funcionalidad**

```bash
# 1. Ir a develop y actualizar
git checkout develop
git pull origin develop

# 2. Crear tu feature branch
git checkout -b feature/mi-funcionalidad

# Ejemplos de nombres:
# feature/google-auth-improvements
# feature/daily-quotes-categories
# feature/user-profile-edit
```

### 2️⃣ **Trabajar en tu Feature**

```bash
# Hacer cambios en archivos...

# Hacer commit de tus cambios
git add .
git commit -m "feat: descripción clara del cambio"

# Más trabajo...
git add .
git commit -m "feat: otro cambio relacionado"
```

### 3️⃣ **Antes de Subir - Actualizar con Develop**

```bash
# 1. Ir a develop y actualizar
git checkout develop
git pull origin develop

# 2. Volver a tu feature
git checkout feature/mi-funcionalidad

# 3. Integrar cambios de develop
git merge develop

# Si hay conflictos, resolverlos y hacer commit
```

### 4️⃣ **Subir tu Feature al Repositorio**

```bash
# Primera vez
git push -u origin feature/mi-funcionalidad

# Siguientes veces
git push origin feature/mi-funcionalidad
```

### 5️⃣ **Crear Pull Request**

1. Ve a GitHub: https://github.com/PedroPortillo0/Practica01Estadia
2. Click en **"Compare & pull request"**
3. **Base**: `develop` ← **Compare**: `feature/mi-funcionalidad`
4. Escribir descripción clara de los cambios
5. Asignar revisor (tu compañero)
6. Click en **"Create pull request"**

### 6️⃣ **Revisar Pull Requests**

```bash
# Si quieres probar la feature de tu compañero localmente:
git fetch origin
git checkout feature/su-funcionalidad
# Probar...
# Si está bien, aprobar en GitHub
```

### 7️⃣ **Después del Merge (Limpiar)**

```bash
# Actualizar develop
git checkout develop
git pull origin develop

# Eliminar tu feature branch local
git branch -d feature/mi-funcionalidad

# (La rama remota se elimina automáticamente en GitHub después del merge)
```

---

## 🔥 Hotfix (Corrección Urgente)

```bash
# 1. Crear hotfix desde main
git checkout main
git pull origin main
git checkout -b hotfix/critical-bug

# 2. Corregir el bug
# ... hacer cambios ...
git add .
git commit -m "fix: corregir bug crítico"

# 3. Crear PR a main
# En GitHub: Base: main ← Compare: hotfix/critical-bug

# 4. Después del merge a main, también mergear a develop
git checkout develop
git pull origin develop
git merge hotfix/critical-bug
git push origin develop
```

---

## 📋 Convenciones de Nombres

### **Ramas:**
```
feature/google-oauth-login
feature/daily-quotes-api
feature/user-profile-page
fix/password-validation
fix/email-verification
hotfix/critical-database-error
```

### **Commits:**
```bash
feat: agregar login con Google
fix: corregir validación de email
refactor: mejorar estructura de UserRepository
docs: actualizar README con nuevas APIs
style: formatear código según PSR-12
test: agregar tests para DailyQuote
chore: actualizar dependencias
```

---

## 🎯 Reglas Importantes

### ✅ **SÍ Hacer:**
1. ✅ Siempre trabajar en feature branches
2. ✅ Hacer commits pequeños y frecuentes
3. ✅ Actualizar develop antes de subir tu feature
4. ✅ Escribir mensajes de commit descriptivos
5. ✅ Revisar el código del compañero antes de aprobar
6. ✅ Comunicarse sobre qué están trabajando

### ❌ **NO Hacer:**
1. ❌ Trabajar directamente en `main` o `develop`
2. ❌ Hacer `git push --force`
3. ❌ Subir código sin probar
4. ❌ Hacer commits gigantes con muchos cambios
5. ❌ Ignorar conflictos (resolverlos correctamente)
6. ❌ Eliminar ramas antes de hacer merge

---

## 🆘 Comandos Útiles

```bash
# Ver todas las ramas
git branch -a

# Ver estado actual
git status

# Ver historial
git log --oneline --graph --all --decorate

# Ver diferencias antes de commit
git diff

# Deshacer último commit (mantener cambios)
git reset --soft HEAD~1

# Descartar cambios en un archivo
git checkout -- nombre-archivo.php

# Ver ramas remotas
git remote -v

# Actualizar referencias remotas
git fetch origin

# Cambiar de rama
git checkout nombre-rama

# Ver qué archivos cambiaron
git diff --name-only develop
```

---

## 👥 Configuración para Nuevo Colaborador

```bash
# 1. Clonar repositorio
git clone https://github.com/PedroPortillo0/Practica01Estadia.git
cd Practica01Estadia

# 2. Configurar Git
git config user.name "Tu Nombre"
git config user.email "tu-email@ejemplo.com"

# 3. Ver ramas disponibles
git branch -a

# 4. Ir a develop
git checkout develop

# 5. Instalar dependencias
composer install
npm install

# 6. Configurar .env
cp .env.example .env
php artisan key:generate

# 7. Base de datos
php artisan migrate
php artisan db:seed

# 8. ¡Listo para trabajar!
git checkout -b feature/mi-primera-funcionalidad
```

---

## 📞 Contacto y Coordinación

**Antes de empezar una feature:**
1. Avisar al equipo en qué van a trabajar
2. Asegurarse de que no haya conflictos de archivos
3. Si van a modificar los mismos archivos, coordinar

**Durante el desarrollo:**
- Hacer push frecuentemente
- Comunicar bloqueos o problemas
- Pedir revisión cuando esté listo

---

## 🎓 Recursos

- **GitHub del Proyecto**: https://github.com/PedroPortillo0/Practica01Estadia
- **Documentación Git Flow**: https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow
- **Convenciones de Commits**: https://www.conventionalcommits.org/

---

## 📈 Estado Actual

- ✅ **main**: Código en producción
- ✅ **develop**: Rama de integración (activa)
- 🔄 **features**: Crear según necesidad

**Última actualización**: Octubre 2025

