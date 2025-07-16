<?php

define('ROOT_PATH', __DIR__ . '/');
$env = parse_ini_file(ROOT_PATH . '.env');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['auth_with_key']) || $_SESSION['auth_with_key'] != true) {
    if (isset($_GET['token']) && $_GET['token'] == $env['ACCESS_TOKEN']) {
        unset($env['ACCESS_TOKEN']);
    } else {
        exit('FORBIDDEN');
    }
}
$_SESSION['auth_with_key'] = true;


if (!isset($_SESSION['output'])) {
    $_SESSION['output'][] = ["NEW GIT SESSION INITIALIZED", date('Y-m-d H:i:s')];
}

$commands = [
    'status' => [
        'instructions' => ['git status']
    ],
    'add' => [
        'instructions' => ['git add . -v']
    ],
    'fetch' => [
        'instructions' => ['git fetch -v']
    ],
    'reset' => [
        'instructions' => ['git reset --hard']
    ],
    'commit' => [
        'instructions' => ['git commit -m "server commit']
    ],
    'pull' => [
        'instructions' => ['git pull -v']
    ],
    'push' => [
        'instructions' => ['git push']
    ],
    'info' => [
        'instructions' => [
            'pwd',
            'php -i',
        ]
    ],
    'config' => [
        'instructions' => [
            'git config user.email "' . $env['GIT_EMAIL'] . '"',
            'git config user.name "' . $env['GIT_NAME'] . '"'
        ]
    ],
    'clear' => [
        'instructions' => [],
        'callback' => function () {
            $_SESSION['output'] = null;
        }
    ],
    'delete' => [
        'hide' => true,
        'instructions' => [],
        'callback' => function () {
            unset($_SESSION['output'][$_GET['index']]);
        }
    ],
];

if (isset($_GET['command']) && in_array($_GET['command'], array_keys($commands))) {
    foreach ($commands[$_GET['command']]['instructions'] as $command) {
        $_SESSION['output'][] = ['Command: ' . $command, shell_exec($command . ' 2>&1')];
    }
    if (isset($commands[$_GET['command']]['callback'])) {
        $commands[$_GET['command']]['callback']();
    }
    header('Location: /?cache=' . time());
    die;
} elseif (isset($_GET['command'])) {
    header('Location: /?cache=' . time());
    die;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git Client</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>@layer properties{@supports (((-webkit-hyphens:none)) and (not (margin-trim:inline))) or ((-moz-orient:inline) and (not (color:rgb(from red r g b)))){*,:before,:after,::backdrop{--tw-border-style:solid;--tw-font-weight:initial}}}@layer theme{:root,:host{--font-sans:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-red-700:oklch(50.5% .213 27.518);--color-blue-600:oklch(54.6% .245 262.881);--color-slate-600:oklch(44.6% .043 257.281);--color-gray-300:oklch(87.2% .01 258.338);--color-gray-500:oklch(55.1% .027 264.364);--color-gray-600:oklch(44.6% .03 256.802);--color-gray-700:oklch(37.3% .034 259.733);--color-gray-800:oklch(27.8% .033 256.848);--color-gray-900:oklch(21% .034 264.665);--color-gray-950:oklch(13% .028 261.692);--color-zinc-600:oklch(44.2% .017 285.786);--color-zinc-800:oklch(27.4% .006 286.033);--color-zinc-900:oklch(21% .006 285.885);--color-neutral-200:oklch(92.2% 0 0);--color-neutral-300:oklch(87% 0 0);--color-neutral-800:oklch(26.9% 0 0);--color-neutral-900:oklch(20.5% 0 0);--color-white:#fff;--spacing:.25rem;--text-sm:.875rem;--text-sm--line-height:calc(1.25/.875);--text-lg:1.125rem;--text-lg--line-height:calc(1.75/1.125);--font-weight-medium:500;--font-weight-semibold:600;--font-weight-bold:700;--radius-sm:.25rem;--radius-lg:.5rem;--radius-xl:.75rem;--radius-2xl:1rem;--radius-3xl:1.5rem;--default-transition-duration:.15s;--default-transition-timing-function:cubic-bezier(.4,0,.2,1);--default-font-family:var(--font-sans);--default-mono-font-family:var(--font-mono)}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}::file-selector-button{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji");font-feature-settings:var(--default-font-feature-settings,normal);font-variation-settings:var(--default-font-variation-settings,normal);-webkit-tap-highlight-color:transparent}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;-webkit-text-decoration:inherit;-webkit-text-decoration:inherit;-webkit-text-decoration:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:var(--default-mono-font-family,ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace);font-feature-settings:var(--default-mono-font-feature-settings,normal);font-variation-settings:var(--default-mono-font-variation-settings,normal);font-size:1em}small{font-size:80%}sub,sup{vertical-align:baseline;font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}:-moz-focusring{outline:auto}progress{vertical-align:baseline}summary{display:list-item}ol,ul,menu{list-style:none}img,svg,video,canvas,audio,iframe,embed,object{vertical-align:middle;display:block}img,video{max-width:100%;height:auto}button,input,select,optgroup,textarea{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}::file-selector-button{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}:where(select:is([multiple],[size])) optgroup{font-weight:bolder}:where(select:is([multiple],[size])) optgroup option{padding-inline-start:20px}::file-selector-button{margin-inline-end:4px}::placeholder{opacity:1}@supports (not ((-webkit-appearance:-apple-pay-button))) or (contain-intrinsic-size:1px){::placeholder{color:currentColor}@supports (color:color-mix(in lab, red, red)){::placeholder{color:color-mix(in oklab,currentcolor 50%,transparent)}}}textarea{resize:vertical}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-date-and-time-value{min-height:1lh;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-datetime-edit{padding-block:0}::-webkit-datetime-edit-year-field{padding-block:0}::-webkit-datetime-edit-month-field{padding-block:0}::-webkit-datetime-edit-day-field{padding-block:0}::-webkit-datetime-edit-hour-field{padding-block:0}::-webkit-datetime-edit-minute-field{padding-block:0}::-webkit-datetime-edit-second-field{padding-block:0}::-webkit-datetime-edit-millisecond-field{padding-block:0}::-webkit-datetime-edit-meridiem-field{padding-block:0}:-moz-ui-invalid{box-shadow:none}button,input:where([type=button],[type=reset],[type=submit]){appearance:button}::file-selector-button{appearance:button}::-webkit-inner-spin-button{height:auto}::-webkit-outer-spin-button{height:auto}[hidden]:where(:not([hidden=until-found])){display:none!important}}@layer components;@layer utilities{.absolute{position:absolute}.relative{position:relative}.top-0{top:calc(var(--spacing)*0)}.top-2{top:calc(var(--spacing)*2)}.right-0{right:calc(var(--spacing)*0)}.right-2{right:calc(var(--spacing)*2)}.col-span-1{grid-column:span 1/span 1}.col-span-4{grid-column:span 4/span 4}.container{width:100%}@media (min-width:40rem){.container{max-width:40rem}}@media (min-width:48rem){.container{max-width:48rem}}@media (min-width:64rem){.container{max-width:64rem}}@media (min-width:80rem){.container{max-width:80rem}}@media (min-width:96rem){.container{max-width:96rem}}.mx-auto{margin-inline:auto}.mt-8{margin-top:calc(var(--spacing)*8)}.mb-2{margin-bottom:calc(var(--spacing)*2)}.block{display:block}.flex{display:flex}.grid{display:grid}.table{display:table}.h-0{height:calc(var(--spacing)*0)}.h-full{height:100%}.max-h-\[80vh\]{max-height:80vh}.max-h-\[200px\]{max-height:200px}.max-h-\[500px\]{max-height:500px}.max-h-\[600px\]{max-height:600px}.max-h-\[800px\]{max-height:800px}.w-1{width:calc(var(--spacing)*1)}.w-1\/5{width:20%}.w-2{width:calc(var(--spacing)*2)}.w-4{width:calc(var(--spacing)*4)}.w-4\/5{width:80%}.w-6{width:calc(var(--spacing)*6)}.w-8{width:calc(var(--spacing)*8)}.w-10{width:calc(var(--spacing)*10)}.grid-cols-5{grid-template-columns:repeat(5,minmax(0,1fr))}.flex-col{flex-direction:column}.gap-2{gap:calc(var(--spacing)*2)}.gap-4{gap:calc(var(--spacing)*4)}.self-stretch{align-self:stretch}.overflow-hidden{overflow:hidden}.overflow-scroll{overflow:scroll}.overflow-y-auto{overflow-y:auto}.rounded{border-radius:.25rem}.rounded-2xl{border-radius:var(--radius-2xl)}.rounded-3xl{border-radius:var(--radius-3xl)}.rounded-lg{border-radius:var(--radius-lg)}.rounded-sm{border-radius:var(--radius-sm)}.rounded-xl{border-radius:var(--radius-xl)}.border{border-style:var(--tw-border-style);border-width:1px}.border-gray-700{border-color:var(--color-gray-700)}.border-gray-800{border-color:var(--color-gray-800)}.border-gray-900{border-color:var(--color-gray-900)}.bg-gray-300{background-color:var(--color-gray-300)}.bg-gray-500{background-color:var(--color-gray-500)}.bg-gray-600{background-color:var(--color-gray-600)}.bg-gray-700{background-color:var(--color-gray-700)}.bg-gray-800{background-color:var(--color-gray-800)}.bg-gray-900{background-color:var(--color-gray-900)}.bg-gray-950{background-color:var(--color-gray-950)}.bg-neutral-200{background-color:var(--color-neutral-200)}.bg-neutral-300{background-color:var(--color-neutral-300)}.bg-neutral-800{background-color:var(--color-neutral-800)}.bg-neutral-900{background-color:var(--color-neutral-900)}.bg-slate-600{background-color:var(--color-slate-600)}.bg-zinc-600{background-color:var(--color-zinc-600)}.bg-zinc-800{background-color:var(--color-zinc-800)}.bg-zinc-900{background-color:var(--color-zinc-900)}.fill-gray-900{fill:var(--color-gray-900)}.p-3{padding:calc(var(--spacing)*3)}.p-6{padding:calc(var(--spacing)*6)}.p-8{padding:calc(var(--spacing)*8)}.px-4{padding-inline:calc(var(--spacing)*4)}.py-2{padding-block:calc(var(--spacing)*2)}.py-3{padding-block:calc(var(--spacing)*3)}.py-8{padding-block:calc(var(--spacing)*8)}.text-center{text-align:center}.text-lg{font-size:var(--text-lg);line-height:var(--tw-leading,var(--text-lg--line-height))}.text-sm{font-size:var(--text-sm);line-height:var(--tw-leading,var(--text-sm--line-height))}.font-bold{--tw-font-weight:var(--font-weight-bold);font-weight:var(--font-weight-bold)}.font-medium{--tw-font-weight:var(--font-weight-medium);font-weight:var(--font-weight-medium)}.font-semibold{--tw-font-weight:var(--font-weight-semibold);font-weight:var(--font-weight-semibold)}.text-gray-300{color:var(--color-gray-300)}.text-gray-500{color:var(--color-gray-500)}.text-gray-600{color:var(--color-gray-600)}.text-gray-700{color:var(--color-gray-700)}.text-gray-800{color:var(--color-gray-800)}.text-white{color:var(--color-white)}.transition{transition-property:color,background-color,border-color,outline-color,text-decoration-color,fill,stroke,--tw-gradient-from,--tw-gradient-via,--tw-gradient-to,opacity,box-shadow,transform,translate,scale,rotate,filter,-webkit-backdrop-filter,backdrop-filter,display,visibility,content-visibility,overlay,pointer-events;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}@media (hover:hover){.hover\:bg-\[\#f05133\]:hover{background-color:#f05133}.hover\:bg-blue-600:hover{background-color:var(--color-blue-600)}.hover\:fill-red-700:hover{fill:var(--color-red-700)}}@media (min-width:64rem){.lg\:w-1\/5{width:20%}.lg\:w-4\/5{width:80%}.lg\:flex-row{flex-direction:row}}}@property --tw-border-style{syntax:"*";inherits:false;initial-value:solid}@property --tw-font-weight{syntax:"*";inherits:false}</style>
</head>

<body class="bg-gray-950 py-8">
    <main class="container mx-auto flex-col lg:flex-row px-4 flex gap-4 max-h-[80vh] overflow-hidden">
        <nav class="lg:w-1/5 gap-2 flex flex-col bg-gray-900 rounded p-6 border border-gray-800 overflow-y-auto">
            <h3 class="flex gap-2 text-gray-300 text-lg font-semibold">
                <svg class="w-6" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewbox="0 0 97 97">
                    <path fill="#F05133" d="M92.71 44.408 52.591 4.291c-2.31-2.311-6.057-2.311-8.369 0l-8.33 8.332L46.459 23.19c2.456-.83 5.272-.273 7.229 1.685 1.969 1.97 2.521 4.81 1.67 7.275l10.186 10.185c2.465-.85 5.307-.3 7.275 1.671 2.75 2.75 2.75 7.206 0 9.958-2.752 2.751-7.208 2.751-9.961 0-2.068-2.07-2.58-5.11-1.531-7.658l-9.5-9.499v24.997c.67.332 1.303.774 1.861 1.332 2.75 2.75 2.75 7.206 0 9.959-2.75 2.749-7.209 2.749-9.957 0-2.75-2.754-2.75-7.21 0-9.959.68-.679 1.467-1.193 2.307-1.537v-25.23c-.84-.344-1.625-.853-2.307-1.537-2.083-2.082-2.584-5.14-1.516-7.698L31.798 16.715 4.288 44.222c-2.311 2.313-2.311 6.06 0 8.371l40.121 40.118c2.31 2.311 6.056 2.311 8.369 0L92.71 52.779c2.311-2.311 2.311-6.06 0-8.371z" />
                </svg>
                Git Client
            </h3>
            <p class="text-gray-300 mb-2">Click a command to run it.</p>
            <?php foreach ($commands as $command => $instructions): ?>
                <?php if (isset($instructions['hide']) && $instructions['hide']) {
                    continue;
                }  ?>
                <a class="rounded-sm block bg-gray-700 px-4 py-2 text-gray-300 text-lg font-semibold hover:bg-[#f05133]"
                    href="?command=<?php echo $command ?>&cache=<?php echo time() ?>"><?php echo ucfirst($command) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="lg:w-4/5 bg-gray-900 rounded p-8 overflow-scroll flex flex-col gap-4  self-stretch border border-gray-800">
            <?php foreach ($_SESSION['output'] as    $i => $message): ?>
                <div class="rounded-lg bg-slate-600 px-4 py-3 relative">
                    <a href="?command=delete&index=<?php echo $i ?>&cache=<?php echo time() ?>" class="absolute top-2 right-2">
                        <svg class="fill-gray-900 hover:fill-red-700" height="10px" width="10px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 460.775 460.775" xml:space="preserve">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M285.08,230.397L456.218,59.27c6.076-6.077,6.076-15.911,0-21.986L423.511,4.565c-2.913-2.911-6.866-4.55-10.992-4.55 c-4.127,0-8.08,1.639-10.993,4.55l-171.138,171.14L59.25,4.565c-2.913-2.911-6.866-4.55-10.993-4.55 c-4.126,0-8.08,1.639-10.992,4.55L4.558,37.284c-6.077,6.075-6.077,15.909,0,21.986l171.138,171.128L4.575,401.505 c-6.074,6.077-6.074,15.911,0,21.986l32.709,32.719c2.911,2.911,6.865,4.55,10.992,4.55c4.127,0,8.08-1.639,10.994-4.55 l171.117-171.12l171.118,171.12c2.913,2.911,6.866,4.55,10.993,4.55c4.128,0,8.081-1.639,10.992-4.55l32.709-32.719 c6.074-6.075,6.074-15.909,0-21.986L285.08,230.397z"></path>
                            </g>
                        </svg>
                    </a>
                    <h3 class="text-gray-300 font-semibold mb-2"><?php echo $message[0] ?></h3>
                    <pre class="bg-gray-800 rounded text-gray-300 p-3 text-sm max-h-[200px] overflow-scroll"><?php echo $message[1] ?></pre>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <div class="container mx-auto px-4 text-gray-800 mt-8">
        <p class="text-center">Tool by <a href="https://antoinebernier.com">Antoine Bernier</a></p>
    </div>

</body>

</html>