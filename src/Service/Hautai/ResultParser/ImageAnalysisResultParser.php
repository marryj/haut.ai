<?php
namespace App\Service\Hautai\ResultParser;

use App\Service\Hautai\ResultParser\ArrayFunctions;

class ImageAnalysisResultParser
{

    use ArrayFunctions;

    const ANALYSIS_FACE = 'face';
    const ANALYSIS_EYE_AREA_CONDITION = 'eye_area_condition';
    const ANALYSIS_HYDRATION_SCORE = 'hydration_score';
    const ANALYSIS_PIGMENTATION_SCORE = 'pigmentation_score';
    const ANALYSIS_WRINKLE_SCORE = 'wrinkles_score';
    const ANALYSIS_PERCEIVED_AGE = 'perceived_age';

    private function getAnalysisValue(array $data, string $parentKey, string $analysisFeatureKey)
    {
        $parentDataPath = $this->recursiveSearchByValue($parentKey, $data);
        array_pop($parentDataPath);
        $parentData = $this->getNestedValue($parentDataPath, $data);

        $analysisDataPath = $this->recursiveSearchByValue($analysisFeatureKey, $parentData);
        if (!is_array($analysisDataPath)) {
            unset($data[$parentDataPath[0]]);
            return $this->getAnalysisValue($data, $parentKey, $analysisFeatureKey);
        } else {
            array_pop($analysisDataPath);
            $res = $this->getNestedValue($analysisDataPath, $parentData);

            return $res['value'];
        }

    }

    /**
     * @param array $data
     * @return mixed
     */
    public function getEyeAreaCondition(array $data)
    {
        return $this->getAnalysisValue($data, self::ANALYSIS_FACE, self::ANALYSIS_EYE_AREA_CONDITION);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function getHydrationScore(array $data)
    {
        return $this->getAnalysisValue($data, self::ANALYSIS_FACE, self::ANALYSIS_HYDRATION_SCORE);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function getPigmentationScore(array $data)
    {
        return $this->getAnalysisValue($data, self::ANALYSIS_FACE, self::ANALYSIS_PIGMENTATION_SCORE);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function geWrinkleScore(array $data)
    {
        return $this->getAnalysisValue($data, self::ANALYSIS_FACE, self::ANALYSIS_WRINKLE_SCORE);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function gePerceivedAge(array $data)
    {
        return $this->getAnalysisValue($data, self::ANALYSIS_FACE, self::ANALYSIS_PERCEIVED_AGE);
    }

}