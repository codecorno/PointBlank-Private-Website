<?php

/**
 * Directive
 *
 * @package Less
 * @subpackage tree
 */
class Less_Tree_Directive extends Less_Tree{

	public $name;
	public $value;
	public $rules;
	public $index;
	public $isReferenced;
	public $currentFileInfo;
	public $debugInfo;
	public $type = 'Directive';

	public function __construct($name, $value = null, $rules, $index = null, $currentFileInfo = null, $debugInfo = null ){
		$this->name = $name;
		$this->value = $value;
		if( $rules ){
			if( is_array($rules) ){
				$this->rules = $rules;
			} else {
				$this->rules = array($rules);

				$sel = new Less_Tree_Selector(array(), $this->index, $this->currentFileInfo);
				$this->rules[0]->selectors = $sel->createEmptySelectors();
			}
			foreach ($this->rules AS $rule){
				$rule->allowImports = true;
			}
		}

		$this->index = $index;
		$this->currentFileInfo = $currentFileInfo;
		$this->debugInfo = $debugInfo;
	}


    public function accept( $visitor ){
		if( $this->rules ){
			$this->rules = $visitor->visitArray( $this->rules );
		}
		if( $this->value ){
			$this->value = $visitor->visitObj( $this->value );
		}
	}


    /**
     * @see Less_Tree::genCSS
     */
    public function genCSS( $output ){
		$value = $this->value;
		$rules = $this->rules;
		$output->add( $this->name, $this->currentFileInfo, $this->index );
		if( $this->value ){
			$output->add(' ');
			$this->value->genCSS($output);
		}
		if( $this->rules ){
			Less_Tree::outputRuleset( $output, $this->rules);
		} else {
			$output->add(';');
		}
	}

	public function compile($env){

		$value = $this->value;
		$rules = $this->rules;

		$mediaPathBackup = $env->mediaPath;
		$mediaBlocksBackup = $env->mediaBlocks;

		$env->mediaPath = array();
		$env->mediaBlocks = array();

		if( $value ){
			$value = $value->compile($env);
		}

		if( $rules ){
			$rules = $rules[0]->compile($env);
			$rules->root = true;
		}

		$env->mediaPath = $mediaPathBackup;
		$env->mediaBlocks = $mediaBlocksBackup;

		return new Less_Tree_Directive( $this->name, $value, $rules, $this->index, $this->currentFileInfo, $this->debugInfo );
	}


	public function variable($name){
		if( $this->rules ){
			// assuming that there is only one rule at this point - that is how parser constructs the rule
			return $this->rules[0]->variable($name);
		}
	}

	public function find($selector){
		if( $this->rules ){
			// assuming that there is only one rule at this point - that is how parser constructs the rule
			return $this->rules[0]->find($selector, $this);
		}
	}

	//rulesets: function () { if (this.rules) return tree.Ruleset.prototype.rulesets.apply(this.rules); },

	public function markReferenced(){
		$this->isReferenced = true;
		if( $this->rules ){
			Less_Tree::ReferencedArray($this->rules);
		}
	}

	public function getIsReferenced(){
		return !isset($this->currentFileInfo['reference']) || !$this->currentFileInfo['reference'] || $this->isReferenced;
	}
}
