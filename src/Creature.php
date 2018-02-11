<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 2017-12-29
 * Time: 10:40 PM
 */

class Creature
{
    private static $ATTRIBUTES = [
        'STR' => 'strength',
        'DEX' => 'dexterity',
        'CON' => 'constitution',
        'INT' => 'intelligence',
        'WIS' => 'wisdom',
        'CHA' => 'charisma',
    ];

    private $name = "Strange Nothing";
    private $race = "Shadow";
    private $class = "-";
    private $alignment = "Neutral Good";
    private $vision = [
        'type' => 'Darkvision',
        'range' => 120
    ];

    private $description = "<p>Nothing to see here!</p>";

    private $temperaments = [];
    private $languages = ['Common', 'Dwarvish', 'Abyssal'];

    private $damageModifiers = [
        'resistances' => ['Necrotic', 'Cold', 'Fire', 'Radiant'],
        'immunities' => ['Force'],
        'vulnerabilities' => ['Slashing'],
        'affinities' => []
    ];

    private $stats = [
        'hitPoints' => 13,
        'armourClass' => 13,
        'speed' => 30,
        'initiative' => 0,
        'passivePerception' => 12,
        'attributes' => [
            'strength' => [
                'score' => 2,
                'modifier' => -4
            ],
            'dexterity' => [
                'score' => 8,
                'modifier' => -1
            ],
            'constitution' => [
                'score' => 4,
                'modifier' => -3
            ],
            'intelligence' => [
                'score' => 12,
                'modifier' => 1
            ],
            'wisdom' => [
                'score' => 18,
                'modifier' => 4
            ],
            'charisma' => [
                'score' => 6,
                'modifier' => -2
            ],
        ],

    ];

    private $skills = [
        'acrobatics' => 4,
        'animalHandling' => 2,
        'arcana' => 0,
        'athletics' => 0,
        'deception' => 0,
        'history' => 0,
        'insight' => 0,
        'intimidation' => 2,
        'investigation' => 0,
        'medicine' => 0,
        'nature' => 2,
        'perception' => 0,
        'performance' => 0,
        'persuasion' => 0,
        'religion' => 0,
        'sleightOfHand' => 4,
        'stealth' => 10,
        'survival' => 0,
    ];


    private $abilities = [
        'attacks' => [],
        'spells' => [],
        'actives' => [],
        'passives' => [],
    ];



    private function toSnakeCase($text)
    {
        return str_replace(' ', '_', $text);
    }

    public function display()
    {
        if (!$this->name) {
            echo '<div class="card error"><span>This creature has no name!</span></div>';
            return false;
        }

        $html = '<div class="creature card">';
        $html .= "<div class='creature__name'><h2>$this->name</h2></div>";
        $html .= '<div class="creature__details">';
        $html .= '<div class="page page__overview">';

        // CREATURE DETAILS
        $html .= '<div class="section creature__general">';
        $html .= "<div class='creature__race'><span class='italic'>$this->race</span></div>";
        $html .= "<div class='creature__class'><span class='italic'>$this->class</span></div>";
        $html .= "<div class='creature__alignment'><span class='italic'>$this->alignment</span></div>";
        $html .= '</div>';


        // STATS
        $html .= '<div class="section stats__primary">';
        $html .= $this->makeBox('Hit Points', [self::makeBig($this->stats['hitPoints'])]);
        $html .= $this->makeBox('Armour Class', [self::makeBig($this->stats['armourClass'])]);
        $html .= $this->makeBox('Speed', [self::makeBig($this->stats['speed'])]);
        $html .= '</div>';

        $html .= '<div class="section stats__attributes">';
        foreach (static::$ATTRIBUTES as $name => $index) {
            $html .= self::makeBox(
                $name,
                [
                    $this->stats['attributes'][$index]['score'],
                    self::makeModifier($this->stats['attributes'][$index]['modifier'])
                ]
            );
        }
        $html .= '</div>';

        $html .= '<div class="section creature__secondary">';
        $html .= $this->makeBox('Initiative', [self::makeModifier($this->stats['initiative'])]);
        $html .= self::makeBox('Passive Perception', [self::makeBig($this->stats['passivePerception'])]);

        if ($this->vision['range'] != 0) {
            $html .= self::makeBox('Vision', [$this->vision['type'] . ', ' . $this->vision['range'] . ' ft.']);
        } else {
            $html .= self::makeBox('Vision', [$this->vision['type']]);
        }
        $html .= '</div>';


        // DAMAGE MODIFIERS
        if (!self::isArrayEmpty($this->damageModifiers)) {
            $html .= '<div class="section creature__damage_modifiers">';
            foreach ($this->damageModifiers as $name => $modifier) {
                if ($this->damageModifiers[$name]) {
                    $html .= '<div class="creature__damage_modifier">';
                    $html .= "<h3>" . ucfirst($name) . ": </h3><span class='italic'>" . implode(', ', $modifier) . "</span>";
                    $html .= '</div>';
                }

            }
            $html .= '</div>';
        }

        // SKILLS
        if (!self::isArrayEmpty($this->skills)) {
            $html .= '<div class="section creature__skills">';
            $bonuses = [];
            foreach ($this->skills as $skill => $bonus) {
                if ($bonus > 0) {
                    $bonuses[] = ucfirst(preg_replace('/[A-Z]/', ' $0', $skill)) . " +$bonus";
                } elseif ($bonus < 0) {
                    $bonuses[] = ucfirst(preg_replace('/[A-Z]/', ' $0', $skill)) . " $bonus";
                }
            }

            $html .= "<h3>Skills: </h3><span class='italic'>" . implode(', ', $bonuses) . "</span>";
            $html .= '</div>';
        }

        // LANGUAGES
        if ($this->languages) {
            $html .= '<div class="section creature__languages">';
            $html .= "<h3>Languages: </h3><span class='italic'>" . implode(', ', $this->languages) . "</span>";
            $html .= '</div>';
        }


        // TEMPERAMENTS
        if ($this->temperaments) {
            $html .= '<div class="section creature__temperament">';
            $html .= "<h3>Temperaments: </h3><span class='italic'>" . implode(', ', $this->temperaments) . "</span>";
            $html .= '</div>';
        }

        $html .= '</div>';

        // ABILITIES
        $html .= '<div class="page page__abilities">';
        if (!self::isArrayEmpty($this->abilities)) {
            foreach ($this->abilities as $abilityType => $ability) {
                if ($this->abilities[$abilityType]) {
                    $html .= '<div class="section">';
                    foreach ($ability as $abilityName => $abilityText) {
                        $title = rtrim(ucfirst($abilityType), "s");
                        $html .= "<div class='ability ability__$abilityType' title='$title'>";
                        $abilityText = self::parseText($abilityText);
                        $html .= "<h3 class='ability__name'>$abilityName </h3><span class='ability__text italic'>$abilityText</span>";
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
            }
        } else {
            $html .= '<div class="section"><span class="subtle">This creature has no listed abilities.</span></div>';
        }
        $html .= '</div>';

        // DESCRIPTION
        if ($this->description) {
            $html .= '<div class="page page__description">';
            $html .= '<div class="section">';
            $html .= "<div class='italic'>$this->description</div>";
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        echo $html;

        return true;
    }

    public function load($file)
    {
        if (!strpos($file, '.json')) {
            $file .= '.json';
        }

        $path = 'creatures/' . $file;
        if (!file_exists($path)) {
            return false;
        }

        $file = fopen($path, 'r');
        $data = fread($file, filesize($path));
        $data = json_decode($data, true);

        $this->name = $data['name'];
        $this->class = $data['class'];
        $this->description = $data['description'];
        $this->alignment = $data['alignment'];
        $this->vision = $data['vision'];
        $this->temperaments = $data['temperaments'];
        $this->languages = $data['languages'];
        $this->damageModifiers = $data['damageModifiers'];
        $this->stats = $data['stats'];
        $this->skills = $data['skills'];
        $this->abilities = $data['abilities'];

        fclose($file);

        return true;
    }

    public function save()
    {
        $data = $this->toArray();

        $file = fopen('creatures/' . self::toSnakeCase($data['name']) . ".json", "w");
        fwrite($file, json_encode($data, JSON_PRETTY_PRINT));
        fclose($file);
    }

    public function toArray()
    {
        $data = [
            'name' => $this->name,
            'race' => $this->race,
            'class' => $this->class,
            'description' => $this->description,
            'alignment' => $this->alignment,
            'vision' => $this->vision,
            'temperaments' => $this->temperaments,
            'languages' => $this->languages,
            'damageModifiers' => $this->damageModifiers,
            'stats' => $this->stats,
            'skills' => $this->skills,
            'abilities' => $this->abilities,
        ];

        return $data;
    }

    private static function makeBox($name, $entries)
    {
        $html = "<div class='box'>";
        $html .= "<h3>$name</h3>";
        $html .= "<ul class='list'>";

        if (is_array($entries)) {
            foreach ($entries as $entry) {

                if (!strpos($entry, '<li')) {
                    if (is_integer($entry) && $entry == 0) {
                        $entry = '-';
                    }
                    $entry = "<li class='italic'>$entry</li>";
                }

                $html .= '<li class="italic">' . $entry . '</li>';
            }
        }

        $html .= "</ul>";
        $html .= '</div>';
        return $html;
    }

    private static function makeBig($text)
    {
        return "<li class='italic big'>$text</li>";
    }

    private static function makeModifier($modifier)
    {
        $html = '';
        if ($modifier > 0) {
            $html .= "<li class='modifier_positive italic big'>+$modifier</li>";
        } elseif ($modifier < 0) {
            $html .= "<li class='modifier_negative italic big'>$modifier</li>";
        } else {
            $html .= "<li class='modifier'>-</li>";
        }
        return $html;
    }

    private function parseText($text)
    {
        foreach (static::$ATTRIBUTES as $name => $value) {
            $modifier = '';
            if ($this->stats['attributes'][$value]['modifier'] > 0) {
                $modifier = " + " . $this->stats['attributes'][$value]['modifier'];
            } elseif ($this->stats['attributes'][$value]['modifier'] < 0) {
                $modifier = " - " . abs($this->stats['attributes'][$value]['modifier']);
            }
            $text = str_replace(" + $$name", $modifier, $text);
        }

        return $text;
    }

    private function isArrayEmpty($array) {
        foreach ($array as $element) {
            if (!empty($element)) {
                return false;
            }
        }
        return true;
    }
}