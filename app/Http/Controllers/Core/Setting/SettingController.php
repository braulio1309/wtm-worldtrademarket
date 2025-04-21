<?php


namespace App\Http\Controllers\Core\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\SettingRequest;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Response;
use App\Models\Core\Auth\User;

class SettingController extends Controller
{

    public function __construct(SettingService $setting)
    {
        $this->service = $setting;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->service->getFormattedSettings();
    }


    /**
     * @param SettingRequest $request
     * @return array
     */
    public function store(SettingRequest $request)
    {
        $setting = $this->service
            ->save();

        return created_responses('setting');
    }


    /**
     * @param SettingRequest $request
     * @return array
     */
    public function update(SettingRequest $request)
    {

        $comisiones = $request->input('comisiones');
        $userSinComision = User::where('commission', '>', 0)->select('id')->get();
        $data = json_decode($comisiones, true);
        $usuariosEnComision = [];
        // Ahora puedes acceder a los valores del array
        foreach ($data as $item) {
            $comision = $item['commission'];
            $usuarios = $item['user'];
            //Actualizar comisiones en cada usuario
            foreach($usuarios as $id){
                $user = User::where('id', $id)->first();
                $user->commission = (float) $comision;
                $user->update();
                $usuariosEnComision[] += $id;
            }
        }

        foreach ($userSinComision as $usuario) {
            if (!in_array($usuario->id, $usuariosEnComision)) {
                $user = User::where('id', $usuario->id)->first();
                $user->commission = 0;
                $user->update();
            }
        }

        $this->service->update();

        notify()
            ->on('settings_updated')
            ->with(trans('default.general_settings'))
            ->send(SettingsNotification::class);

        return updated_responses('settings', [
            'settings' => $this->service->getFormattedSettings()
        ]);
    }


    /**
     * @return array
     */
    public function destroy()
    {
        if ($this->service->delete()) {
            return deleted_responses('setting');
        }
        return failed_responses();
    }
}
