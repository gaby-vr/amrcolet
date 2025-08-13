<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Package;
use App\Models\Template;
use App\Models\User;
use App\Traits\OrderValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    use OrderValidationTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'templates',
            'subsection' => null,
            'title' => __('Sabloane')
        ]);
    }

    public function get(Request $request, Template $template)
    {
        if($template->user_id == auth()->id()) {
            return response()->json(['items' => $template->toArray(), 'subitems' => $template->packages->toArray(), 'status' => 200]);
        } else {
            return response()->json(['status' => 422]);
        }
    }

    public function update(Request $request, Template $template = null)
    {
        if($template != null && $template->user_id != auth()->id()) {
            return redirect()->back();
        }

        if($template != null) {
            session()->flash('edit', $template->id);
        }
        session()->flash('validated', true);

        $attributes = $request->validate([
            'favorite' => ['nullable', 'string', Rule::in(['1'])],
            'name' => ['required', 'string', 'min:2', 'max:255'],
        ] + array_intersect_key($this->packageRules(), array_flip([
            'type','nr_colete','content',
            'weight','weight.*','length','length.*',
            'width','width.*','height','height.*',
            'volume','volume.*',
        ])),[],$this->packageNames());

        // if ($validated->fails()) {
        //     return redirect()->back()->withErrors($validated->errors()->all());
        // }

        session()->pull('edit');
        session()->pull('validated');

        $attributes['favorite'] = $attributes['favorite'] ?? 0;

        if($template == null) {
            $attributes['user_id'] = auth()->id();
            $template = Template::create($attributes);

        }
        self::updateTemplate($template, $attributes);

        return redirect()->back()->with([
            'success' => __('Informatiile au fost salvate')
        ]);
    }

    public function updateTemplate(Template $template, $attributes)
    {
        $template->packages()->delete();
        if($attributes['type'] == '1' && $attributes['nr_colete'] > 0) {
            $attributes['total_volume'] = 0;
            $attributes['total_weight'] = 0;
            for($i = 0 ; $i < $attributes['nr_colete'] ; $i++)
            {
                $package = Package::create([
                    'livrare_id' => 0,
                    'template_id' => $template->id,
                    'weight' => $attributes['weight'][$i],
                    'width' => $attributes['width'][$i],
                    'length' => $attributes['length'][$i],
                    'height' => $attributes['height'][$i],
                    'volume' => round(round($attributes['width'][$i] * $attributes['length'][$i] * $attributes['height'][$i], 2)/6000, 2),
                ]);

                $attributes['total_volume'] += $package->volume;
                $attributes['total_weight'] += $package->weight;
            }
        }
        unset($attributes['weight']);
        unset($attributes['width']);
        unset($attributes['length']);
        unset($attributes['height']);
        Template::where('id', $template->id)->update($attributes);
    }

    public function delete(Request $request, Template $template)
    {
        $template->packages()->delete();
        $template->delete();
        // Package::where('template_id', $template->id)->delete();
        // Template::where('id', $template->id)->delete();
        session()->flash('success', __('Sablonul a fost sters'));
        return redirect()->back();
    }
}
