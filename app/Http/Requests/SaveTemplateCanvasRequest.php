<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveTemplateCanvasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() && $this->route('template')?->isVisual();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $sectionNames = array_keys($this->route('template')->orderableSections());
        $canvasNames = ['cover', ...$sectionNames, 'closing'];

        return [
            'primary_color' => ['required', 'hex_color'],
            'text_color' => ['required', 'hex_color'],
            'background_color' => ['required', 'hex_color'],
            'heading_font' => ['required', Rule::in(['serif', 'sans', 'monospace'])],
            'body_font' => ['required', Rule::in(['sans', 'serif'])],
            'section_order' => ['required', 'array', 'size:'.count($sectionNames)],
            'section_order.*' => ['required', 'string', 'distinct', Rule::in($sectionNames)],
            'sections' => ['required', 'array:quote,countdown,events,story,gallery,rsvp,gift,wishes'],
            'sections.*' => ['required', 'boolean'],
            'canvas' => ['required', 'array:'.implode(',', $canvasNames), 'size:'.count($canvasNames)],
            'canvas.*' => ['required', 'array:background,height,content_x,content_top,content_width,content_align,elements'],
            'canvas.*.background' => ['required', 'hex_color'],
            'canvas.*.height' => ['required', 'integer', 'between:360,1000'],
            'canvas.*.content_x' => ['required', 'numeric', 'between:0,70'],
            'canvas.*.content_top' => ['required', 'integer', 'between:0,300'],
            'canvas.*.content_width' => ['required', 'numeric', 'between:30,100'],
            'canvas.*.content_align' => ['required', Rule::in(['left', 'center', 'right'])],
            'canvas.*.elements' => ['present', 'array', 'max:30'],
            'canvas.*.elements.*' => ['required', 'array:id,kind,x,y,width,height,font_size,rotation,opacity,color,content,image,shape'],
            'canvas.*.elements.*.id' => ['required', 'alpha_dash', 'max:64'],
            'canvas.*.elements.*.kind' => ['required', Rule::in(['text', 'shape', 'image'])],
            'canvas.*.elements.*.x' => ['required', 'numeric', 'between:0,100'],
            'canvas.*.elements.*.y' => ['required', 'numeric', 'between:0,100'],
            'canvas.*.elements.*.width' => ['required', 'numeric', 'between:5,100'],
            'canvas.*.elements.*.height' => ['required', 'numeric', 'between:2,100'],
            'canvas.*.elements.*.font_size' => ['required', 'integer', 'between:10,96'],
            'canvas.*.elements.*.rotation' => ['required', 'integer', 'between:-180,180'],
            'canvas.*.elements.*.opacity' => ['required', 'numeric', 'between:0,1'],
            'canvas.*.elements.*.color' => ['required', 'hex_color'],
            'canvas.*.elements.*.content' => ['nullable', 'string', 'max:300'],
            'canvas.*.elements.*.image' => ['nullable', 'string', 'max:250', 'regex:/^storage\/template-canvas\/'.$this->route('template')->id.'\/[a-f0-9-]+\.webp$/'],
            'canvas.*.elements.*.shape' => ['nullable', Rule::in(['circle', 'rectangle', 'line'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = $this->input('payload');
        if (is_string($payload) && strlen($payload) <= 100000) {
            $decoded = json_decode($payload, true);
            if (is_array($decoded)) {
                $this->merge($decoded);
            }
        }
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            foreach ((array) $this->input('canvas', []) as $section => $canvas) {
                if (! is_array($canvas)) {
                    continue;
                }

                if (isset($canvas['content_x'], $canvas['content_width']) && is_numeric($canvas['content_x']) && is_numeric($canvas['content_width']) && (float) $canvas['content_x'] + (float) $canvas['content_width'] > 100) {
                    $validator->errors()->add("canvas.$section.content_x", 'Konten melewati tepi kanan kanvas.');
                }

                foreach ((array) ($canvas['elements'] ?? []) as $index => $element) {
                    if (! is_array($element)) {
                        continue;
                    }

                    if (isset($element['x'], $element['width']) && is_numeric($element['x']) && is_numeric($element['width']) && (float) $element['x'] + (float) $element['width'] > 100) {
                        $validator->errors()->add("canvas.$section.elements.$index.x", 'Elemen melewati tepi kanan kanvas.');
                    }
                    if (isset($element['y'], $element['height']) && is_numeric($element['y']) && is_numeric($element['height']) && (float) $element['y'] + (float) $element['height'] > 100) {
                        $validator->errors()->add("canvas.$section.elements.$index.y", 'Elemen melewati tepi bawah kanvas.');
                    }
                    if (($element['kind'] ?? null) === 'image' && empty($element['image'])) {
                        $validator->errors()->add("canvas.$section.elements.$index.image", 'Pilih gambar untuk elemen ini.');
                    }
                    if (($element['kind'] ?? null) === 'shape' && empty($element['shape'])) {
                        $validator->errors()->add("canvas.$section.elements.$index.shape", 'Pilih bentuk ornamen.');
                    }
                }
            }
        }];
    }
}
