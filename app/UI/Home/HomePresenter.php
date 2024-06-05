<?php

declare(strict_types=1);

namespace App\UI\Home;

use Nette;
use App\Model\HomeFacade;
use Nette\Application\UI\Form;


final class HomePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(private HomeFacade $homeFacade)
    {
    }

    public function renderDefault()
    {
    }

    public function renderChuck()
    {
        $image = $this->homeFacade->drawJokeOnImage();
        $this->template->image = $image;
    }

    public function renderInitials()
    {
        $initials = $this->homeFacade->filterInitials();
        $this->template->initials = $initials;
    }

    public function renderCalculation()
    {
        $calculations = $this->homeFacade->filterCalculations();
        $this->template->calculations = $calculations;
    }

    public function renderDates()
    {
        $dates = $this->homeFacade->filterDates();
        $this->template->dates = $dates;
    }

    public function renderEval()
    {
        $evaluations = $this->homeFacade->filterWithoutEval();
        $this->template->evaluations = $evaluations;
    }

    protected function createComponentChuck(): Form
    {
        $form = new Form();

        $form->addSubmit('chuck', 'Úkol 1 - Chuck Norris')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'chuckRedirect'];

        return $form;
    }


    protected function createComponentInitials(): Form
    {
        $form = new Form();

        $form->addSubmit('initials', 'Úkol 2 - Stejné iniciály')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'initialsRedirect'];

        return $form;
    }

    protected function createComponentCalculations(): Form
    {
        $form = new Form();

        $form->addSubmit('initials', 'Úkol 3 - Výpočet')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'calculationsRedirect'];

        return $form;
    }

    protected function createComponentDates(): Form
    {
        $form = new Form();

        $form->addSubmit('initials', 'Úkol 4 - createdAt')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'datesRedirect'];

        return $form;
    }


    protected function createComponentEval(): Form
    {
        $form = new Form();

        $form->addSubmit('initials', 'Úkol 5 - Eval bez evalu')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'evalRedirect'];

        return $form;
    }

    public function chuckRedirect(Form $form, $data): void
    {
        $this->redirect('Home:chuck');
    }

    public function calculationsRedirect(Form $form, $data): void
    {
        $this->redirect('Home:calculation');
    }

    public function initialsRedirect(Form $form, $data): void
    {
        $this->redirect('Home:initials');
    }

    public function datesRedirect(Form $form, $data): void
    {
        $this->redirect('Home:dates');
    }

    public function evalRedirect(Form $form, $data): void
    {
        $this->redirect('Home:eval');
    }
}
