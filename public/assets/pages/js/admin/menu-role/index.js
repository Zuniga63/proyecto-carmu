Vue.component('menu-item', {
  props:['menu', 'roles', 'rolMenu', 'level'],
  methods: {
    onClick(menu, roleId, roleName, checked){
      const data = {
        menu,
        roleId,
        roleName,
        checked
      }

      this.$emit('menu-checked', data);
    }
  },
  template: /*html*/`
  <tr>
    <td :class="{'font-weight-bold': level == 0,  'pl-10': level== 0, 'pl-20': level==1, 'pl-30': level==2, 'pl-40': level==3}"> 
      <i class="fas fa-arrows-alt" v-if="level == 0"></i>
      <i class="fas fa-arrow-right" v-else></i>
      {{menu.name}} 
    </td>
    <td 
      class="text-center" 
      v-for="(roleName, roleId) in roles" 
      :key="roleId"
    >
      <input 
        type="checkbox" 
        name="menu_rol" 
        class="menu_rol" 
        @click="onClick(menu, roleId, roleName, $event.target.checked)"
        :checked="rolMenu[roleId].some(menuRol => menuRol.id == menu.id)"
      >
    </td>
  </tr>`
})

Vue.component('menu-list', {
  props: ['roles', 'menus', 'rolMenu'],
  methods: {
    onChecked(data){
      this.$emit('menu-checked', data);
    }
  },
  template:/*html*/`
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header with-border">
            <h3 class="card-title">Asignacion de menús</h3>
          </div><!-- /.card-header -->
          <div 
            class="card-body table-responsive p-0" 
            style="height: 300px;"
          >
            <table class="table table-head-fixed table-hover table-striped text-nowrap">
              <thead>
                <tr>
                  <th>Menús</th>
                  <th class="text-center" v-for="(roleName, id) in roles" :key="id">{{roleName}}</th>
                </tr>
              </thead>
              <tbody>
                <!-- Menus de primer nivel -->
                <template v-for="menu of menus" :key="menu.id">
                  <menu-item 
                    :menu="menu" 
                    :roles="roles" 
                    :rol-menu="rolMenu" 
                    level="0"
                    @menu-checked="onChecked"
                  />
                  <!-- Menus de segundo nivel -->
                  <template v-for="menuChild1 of menu.submenus" v-if="menu.submenus.length > 0">
                  <menu-item :menu="menuChild1" :roles="roles" :rol-menu="rolMenu" level="1" @menu-checked="onChecked"/>
                    <!-- Menus de tercer nivel -->
                    <template v-for="menuChild2 of menuChild1.submenus" v-if="menuChild1.submenus.length > 0">
                      <menu-item :menu="menuChild2" :roles="roles" :rol-menu="rolMenu" level="2" @menu-checked="onChecked"/>
                      <!-- Menus de cuarto nivel -->
                      <template v-for="menuChild3 of menuChild2.submenus" v-if="menuChild2.submenus.length > 0">
                        <menu-item :menu="menuChild3" :roles="roles" :rol-menu="rolMenu" level="3" @menu-checked="onChecked"/>
                      </template><!-- /.Cuarto nivel-->
                    </template><!-- /.Tercer nivel-->
                  </template><!-- /.Segundo nivel-->
                </template><!-- /.Primer nivel -->
              </tbody>
            </table>
          </div><!-- /.card-body -->
        </div><!-- /.card -->
      </div><!-- /.col-lg-12 -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
  `
})

const app = new Vue({
  el: '#app',
  data: {
    roles: baseRoles,
    menus: baseMenus,
    roleHasMenu: rolHasMenu,
    token: _token,
    url: url
  },
  methods:{
    async onMenuChecked(data){
      let stateData = {
        menu_id: data.menu.id,
        role_id: data.roleId,
        state: data.checked ? 1 : 0
      }

      const res = await fetch(this.url, {
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json, text-plain, */*",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": this.token
        },
        method: 'post',
        body: JSON.stringify(stateData),
      })

      const resData = await res.json();
      if(res.ok){
        let message = "";
        let title = '';
        let type = "success";

        if(data.checked){
          title = "¡Menu asignado!"
          message = `El menu ${data.menu.name} ha sido asignado correctamente al rol ${data.roleName}`;
        }else{
          title = "¡Menu desasignado!";
          type = "info";
          message = `El menu ${data.menu.name} ha sido desasignado correctamente del rol ${data.roleName}`;
        }

        functions.notifications(message, title, type);
      }else{
        functions.notifications('La petición fue rechazada', 'Error en la petición', 'warning');
      }
    }
  }  
}
)