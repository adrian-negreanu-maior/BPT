<?php

namespace App\Service;

use App\Service\Exception\CannotPrepareException;
use App\Service\Exception\InvalidOptionException;
use App\Service\Exception\InvalidParameterException;

class BeveragePreparationService
{
    /**
     * @var int[]
     */
    private static array $currencyLoad = [
        '1' => 100,
        '5' => 50,
        '10' => 25
    ];
    /**
     * @var int[]
     */
    private static array $ingredientsLoad = [
        'water' => 100,
        'tea_leaves' => 100,
        'cocoa' => 100,
        'milk' => 100,
        'sugar' => 100,
        'powder_milk' => 100,
        'coffee_beans' => 100,
    ];

    /**
     * @var mixed|[]
     */
    private static array $options = [
        '1' => [
            'name' => 'tea',
            'ingredients' => ['water','tea_leaves','sugar'],
            'price' => 2
        ],
        '2' => [
            'name' => 'coffee',
            'ingredients' => ['water','coffee_beans','sugar'],
            'price' => 6
        ],
        '3' => [
            'name' => 'latte',
            'ingredients' => ['water','coffee_beans','milk','sugar'],
            'price' => 7
        ],
        '4' => [
            'name' => 'cappuccino',
            'ingredients' => ['water','coffee_beans','cocoa','milk','sugar'],
            'price' => 4
        ],
        '5' => [
            'name' => 'cocoa',
            'ingredients' => ['water','cocoa','milk','sugar'],
            'price' => 4
        ],
        '6' => [
            'name' => 'hot chocolate',
            'ingredients' => ['water','cocoa','powder_milk','sugar'],
            'price' => 3
        ],
    ];

    /**
     * @throws InvalidParameterException
     * @throws CannotPrepareException
     * @throws InvalidOptionException
     */
    public function validateParams(string $beverageID, string $paidAmount): bool
    {
        if ($beverageID != (int)$beverageID) {
          throw new InvalidParameterException("Please use an integer ID for the beverage");
        }

        if (!array_key_exists($beverageID, self::$options)) {
          throw new InvalidOptionException("Unknown option selected");
        }

        if ($paidAmount != (int)$paidAmount) {
          throw new InvalidParameterException("Rounded amount shall be entered");
        }

        if (self::$options[$beverageID]['price'] > (int)$paidAmount) {
          throw new InvalidParameterException("Insufficient funds for the desired option. Please try again.");
        }

        return $this->canPrepare(self::$options[$beverageID]);
    }

    public function prepare($beverageID, $paidAmount): void
    {
        $beverage = self::$options[$beverageID];
        $this->displayBeverage($beverage);

        $this->consumeIngredients($beverage['ingredients']);

        $this->updateInternalCash($paidAmount);

        $this->payChange($beverage, $paidAmount);

        $this->displayStock();
    }

    protected function displayBeverage($beverage): void
    {
        $ingredients = implode('|', $beverage['ingredients']);
        echo "Please wait while we prepare your order\n";
        echo "Beverage: {$beverage['name']}\n";
        echo "Ingredients: {$ingredients}\n";
        echo "Price: {$beverage['price']}\n";
    }

    public function displayMenu(): string
    {
        $options = self::$options;
        $optionItems = [];
        foreach ($options as $key => $beverage) {
          $optionItems[] = "$key. {$beverage['name']} --- {$beverage['price']} RON";
        }

        return implode(' | ', $optionItems);
    }

    /**
     * @throws CannotPrepareException
     */
    protected function canPrepare($beverage): bool
    {
        $beverageIngredients = $beverage['ingredients'];

        foreach ($beverageIngredients as $requiredIngredient) {
          if (self::$ingredientsLoad[$requiredIngredient] <=0){
              throw new CannotPrepareException("Currently not all the ingredients are available. Please select something else");
          }
        }

        return true;
    }

    protected function displayStock(): void
    {
        foreach (self::$ingredientsLoad as $name => $stock) {
            echo "$name: $stock\n";
        }
    }

    protected function payChange(array $beverage, $paidAmount): void
    {
        $beveragePrice = $beverage['price'];
        $cashback = $paidAmount - $beveragePrice;

        $back = [
            '1' => 0,
            '5' => 0,
            '10' => 0,
        ];

        $back['10'] = (int)($cashback/10);
        $cashback %= 10;
        $back['5'] = (int)($cashback/5);
        $cashback %= 5;
        $back['1'] = $cashback;

        echo "*******\n";
        echo "Thank you for your order, please pick your change:\n";
        foreach ($back as $key=> $value) {
            echo "$key -- $value\n";
        }
        echo "*******\n";
    }

    /**
     * @param int $paidAmount
     */
    protected function updateInternalCash(int $paidAmount): void
    {
    // assuming the entered amount was optimally paid (the least number of banknotes)
        $cashReceived = $paidAmount;

        self::$currencyLoad['10'] += (int)($cashReceived / 10);
        $cashReceived %= 10;
        self::$currencyLoad['5'] += (int)($cashReceived / 5);
        $cashReceived %= 5;
        self::$currencyLoad['1'] += $cashReceived;
    }

    /**
     * @param array $ingredients
     */
    protected function consumeIngredients(array $ingredients): void
    {
        foreach ($ingredients as $ingredient) {
            self::$ingredientsLoad[$ingredient]--;
        }
    }
}